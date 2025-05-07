<?php
require_once 'backend/config.php';
require_once __DIR__ . '/vendor/autoload.php';

// Set Midtrans configuration
\Midtrans\Config::$serverKey = 'SB-Mid-server-POVgPWLqPgTc6ZOihxtbB2Jz';
\Midtrans\Config::$isProduction = false;

// Initialize logging
file_put_contents('midtrans_callback.log', "[" . date('Y-m-d H:i:s') . "] Callback received\n", FILE_APPEND);

// Get POST data from Midtrans
$json_result = file_get_contents('php://input');
file_put_contents('midtrans_callback.log', "Raw data: " . $json_result . "\n", FILE_APPEND);

$result = json_decode($json_result);

if (!$result) {
    file_put_contents('midtrans_callback.log', "Invalid JSON data received\n", FILE_APPEND);
    http_response_code(400);
    exit();
}

try {
    // Verify the notification using Midtrans class
    $notif = new \Midtrans\Notification();
    
    // Extract important data
    $transaction = $notif->transaction_status;
    $type = $notif->payment_type;
    $order_id = $notif->order_id;
    $fraud = $notif->fraud_status;
    $gross_amount = $notif->gross_amount;
    
    file_put_contents('midtrans_callback.log', "Processing order: $order_id, status: $transaction\n", FILE_APPEND);

    // Check database connection
    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    // Update payment status in database
    $sql = "UPDATE payments SET 
            status = ?,
            payment_type = ?,
            fraud_status = ?,
            amount = ?,
            transaction_time = ?,
            settlement_time = ?,
            raw_response = ?
            WHERE order_id = ?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $transaction_time = !empty($notif->transaction_time) ? date('Y-m-d H:i:s', strtotime($notif->transaction_time)) : null;
    $settlement_time = !empty($notif->settlement_time) ? date('Y-m-d H:i:s', strtotime($notif->settlement_time)) : null;
    
    $bind_result = $stmt->bind_param(
        "ssssssss",
        $transaction,
        $type,
        $fraud,
        $gross_amount,
        $transaction_time,
        $settlement_time,
        $json_result,
        $order_id
    );
    
    if (!$bind_result) {
        throw new Exception("Bind failed: " . $stmt->error);
    }
    
    $execute_result = $stmt->execute();
    if (!$execute_result) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $affected_rows = $stmt->affected_rows;
    $stmt->close();
    
    file_put_contents('midtrans_callback.log', "Updated $affected_rows rows in payments table\n", FILE_APPEND);

    // If payment is successful, update project status
    if ($transaction == 'settlement' || $transaction == 'capture') {
        if ($fraud == 'accept') {
            // Get project_id from payment
            $sql = "SELECT project_id FROM payments WHERE order_id = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            
            $stmt->bind_param("s", $order_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $payment = $result->fetch_assoc();
                $project_id = $payment['project_id'];
                $stmt->close();
                
                // Update project status to in_progress
                $update_sql = "UPDATE projects SET status = 'in_progress' WHERE id = ?";
                $stmt = $conn->prepare($update_sql);
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }
                
                $stmt->bind_param("i", $project_id);
                $execute_result = $stmt->execute();
                
                if (!$execute_result) {
                    throw new Exception("Project update failed: " . $stmt->error);
                }
                
                $affected_rows = $stmt->affected_rows;
                $stmt->close();
                
                file_put_contents('midtrans_callback.log', "Updated $affected_rows projects to in_progress\n", FILE_APPEND);
            } else {
                file_put_contents('midtrans_callback.log', "No project found for order $order_id\n", FILE_APPEND);
            }
        }
    }
    
    http_response_code(200);
    echo "Callback processed successfully";
    
} catch (Exception $e) {
    $error_msg = "Payment callback error: " . $e->getMessage();
    error_log($error_msg);
    file_put_contents('midtrans_callback.log', $error_msg . "\n", FILE_APPEND);
    http_response_code(500);
    echo $error_msg;
}

$conn->close();
?>
