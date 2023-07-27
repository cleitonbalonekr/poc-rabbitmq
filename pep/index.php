<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;


$app = AppFactory::create();


// Route to send message to RabbitMQ queue
$app->post('/send-message', function (Request $request, Response $response) {
  $rabbitmq_host = 'rabbitmq'; // This is the hostname of the RabbitMQ container in Docker Compose
  $rabbitmq_port = 5672;
  $rabbitmq_user = 'admin';
  $rabbitmq_pass = 'admin';
  $queue_name = 'patients';

  // Create a connection to RabbitMQ
  $connection = new AMQPStreamConnection($rabbitmq_host, $rabbitmq_port, $rabbitmq_user, $rabbitmq_pass);

  // Create a channel
  $channel = $connection->channel();

  // Declare a queue to ensure it exists
  $channel->queue_declare($queue_name, false, false, false, false);

  // Get the message data from the POST request
  $message_data = $request->getBody();
  // Create a new message with the body
  $message = new AMQPMessage($message_data);

  // Publish the message to the queue
  $channel->basic_publish($message, '', $queue_name);

  $channel->close();
  $connection->close();

  // Respond with a success message
  $response->getBody()->write('Message sent to RabbitMQ queue successfully');
  return $response->withHeader('Content-Type', 'text/plain')->withStatus(200);
});

// // Run the Slim app
$app->run();
