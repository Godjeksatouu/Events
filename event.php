<?php
session_start();
require 'config.php';

$eventId = isset($_GET['eventId']) ? $_GET['eventId'] : '';
$editionId = isset($_GET['editionId']) ? $_GET['editionId'] : '';

if (!$eventId || !$editionId) {
  echo 'Invalid event selection';
  exit();
}

$query = 'SELECT e.eventTitle, e.eventDescription, e.eventType, e.TariffNormal, e.TariffReduit,
       ed.image, ed.dateEvent, ed.timeEvent, ed.NumSalle, s.capSalle
FROM evenement e
JOIN edition ed ON e.eventId = ed.eventId
JOIN salle s ON ed.NumSalle = s.NumSalle
WHERE e.eventId = :eventId AND ed.editionId = :editionId
';
$stmt = $pdo->prepare($query);
$stmt->bindParam(':eventId', $eventId);
$stmt->bindParam(':editionId', $editionId);
$stmt->execute();
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
  echo 'Event not found';
  exit();
}


$queryReserved = '
  SELECT SUM(r.qteBilletsNormal) AS reservedNormal, SUM(r.qteBilletsReduit) AS reservedReduit
  FROM reservation r
  WHERE r.editionId = :editionId';

$stmtReserved = $pdo->prepare($queryReserved);
$stmtReserved->bindParam(':editionId', $editionId);
$stmtReserved->execute();
$reserved = $stmtReserved->fetch(PDO::FETCH_ASSOC);

$totalReserved = ($reserved['reservedNormal'] ?? 0) + ($reserved['reservedReduit'] ?? 0);
$remainingTickets = max(0, $event['capSalle'] - $totalReserved);


?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Events </title>
  <link rel="stylesheet" href="css/event.css?v=<?php echo time(); ?>">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
</head>

<body>

  <!-- Header -->
  <?php include 'header.php'; ?>

  <div class="container">
    <div class="main-content">
      <div class="card">
        <img class="event-img" src="<?php echo htmlspecialchars($event['image']); ?>"
          alt="Conference image showing people at an event with purple lighting">

        <h2>Event Information</h2>
        <p><?php echo htmlspecialchars($event['eventTitle']) ?></p>

        <h2>Event About</h2>
        <p><?php echo htmlspecialchars($event['eventDescription']); ?></p>
      </div>

    </div>

    <div class="sidebar">
      <div class="card">
        <div class="date-info">
          <span class="date-label">Date :</span>
          <span class="date-value"><?php echo date('d M Y', strtotime($event['dateEvent'])); ?></span>
        </div>

        <div class="date-info">
          <span class="date-label">Time :</span>
          <span class="date-value"><?php echo date('H:i', strtotime($event['timeEvent'])); ?></span>
        </div>

      </div>

      <div class="card">
        <h2>Tickets</h2>
        <form action="purchase.php" method="POST">
        <input type="hidden" name="editionId" value="<?php echo htmlspecialchars($editionId); ?>">

        <div class="ticket-type">
          <div class="ticket-type-header">
            <span class="ticket-type-name">Normal</span>
            <span class="tickets-remaining">(<?php echo $remainingTickets; ?> seats remaining)</span>
          </div>

          <div class="ticket-details">
            <span>Ticket Normal:</span>
            <span><?php echo number_format($event['TariffNormal'], decimals: 2); ?></span>
          </div>

          <div class="ticket-details">
            <span>Quantity:</span>
            <div class="quantity-selector">
            <input type="number" name="qteBilletsNormal" value="0" min="0" step="1" class="quantity-input">
            </div>
          </div>

          <div class="ticket-details">
            <span>Subtotal:</span>
            <span class="subtotal">$0.00</span>
          </div>
        </div>

        <div class="ticket-type">
          <div class="ticket-type-header">
            <span class="ticket-type-name">Reduit</span>
            <span class="tickets-remaining">(<?php echo $remainingTickets; ?> seats remaining)</span>
          </div>

          <div class="ticket-details">
            <span>Ticket Reduit:</span>
            <span><?php echo number_format($event['TariffReduit'], 2); ?></span>
          </div>

          <div class="ticket-details">
            <span>Quantity:</span>
            <div class="quantity-selector">
            <input type="number" name="qteBilletsReduit" value="0" min="0" step="1" class="quantity-input">
            </div>
          </div>

          <div class="ticket-details">
            <span>Subtotal:</span>
            <span class="subtotal">$0.00</span>
          </div>

          <div class="ticket-details">
          <div class="total-row">
          <span class="quantity-value">Quantity: 0</span>
          <span class="total-value">Total: $0.00</span>
          </div>
          </div>
          <button class="get-tickets-btn">Get Your Tickets</button>
        </div>
        </form>


      </div>

      </div>
    </div>
  </div>
</body>

<script>
  document.querySelectorAll('.quantity-input').forEach(input => {
    input.addEventListener('input', function () {
      updateSubtotalAndTotal();
    });
  });

  
  function updateSubtotalAndTotal() {
  let totalQuantity = 0;
  let totalPrice = 0;

  document.querySelectorAll('.ticket-type').forEach(ticket => {
    const price = parseFloat(ticket.querySelectorAll('.ticket-details span')[1].textContent.replace('$', '').trim());
    const quantity = parseInt(ticket.querySelector('.quantity-input').value) || 0;

    const subtotal = price * quantity;
    ticket.querySelector('.subtotal').textContent = `$${subtotal.toFixed(2)}`;

    totalQuantity += quantity;
    totalPrice += subtotal;
  });

  // Update the final quantity and total
  document.querySelector('.quantity-value').textContent = `Quantity: ${totalQuantity}`;
  document.querySelector('.total-value').textContent = `Total: $${totalPrice.toFixed(2)}`;
}




</script>

</html>