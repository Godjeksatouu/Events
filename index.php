<?php
session_start();
require 'config.php';

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : '';
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';


// Initial query
$query = 'SELECT e.eventId, ed.editionId, e.eventTitle AS event_title, e.eventDescription AS event_description, 
                 e.eventType AS typeEvent, e.TariffNormal, e.TariffReduit, ed.image AS event_image, 
                 ed.dateEvent AS edition_date, ed.timeEvent AS edition_time, ed.NumSalle, s.capSalle,
                 (SELECT SUM(r.qteBilletsNormal) + SUM(r.qteBilletsReduit) FROM reservation r WHERE r.editionId = ed.editionId) AS total_reserved
          FROM evenement e
          JOIN edition ed ON e.eventId = ed.eventId
          JOIN salle s ON ed.NumSalle = s.NumSalle
          WHERE ed.dateEvent >= CURDATE()';

// Add search condition if searchTerm is set
if ($searchTerm) {
    $query .= ' AND e.eventTitle LIKE :searchTerm';
}

// Add date conditions if startDate and endDate are set
if ($startDate && $endDate) {
    $query .= ' AND ed.dateEvent BETWEEN :startDate AND :endDate';
} else {
    if ($startDate) {
        $query .= ' AND ed.dateEvent >= :startDate';
    }
    if ($endDate) {
        $query .= ' AND ed.dateEvent <= :endDate';
    }
}

// Add category condition if category is set
if ($category) {
    $query .= ' AND e.eventType = :category';
}

$stmt = $pdo->prepare($query);

// Bind parameters
if ($searchTerm) {
    $stmt->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
}

if ($startDate) {
    $stmt->bindParam(':startDate', $startDate, PDO::PARAM_STR);
}

if ($endDate) {
    $stmt->bindParam(':endDate', $endDate, PDO::PARAM_STR);
}

if ($category) {
    $stmt->bindParam(':category', $category, PDO::PARAM_STR);
}

$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventPlace - Discover Events</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
</head>

<body>

    <!-- Header -->
    <?php include 'header.php'; ?>


    <!-- Hero Section -->
    <section class="hero">
    <div class="hero-content">
        <p>Find Your Next Experience</p>
        <h1>Discover & Promote Upcoming Events</h1>
    </div>
</section>
    <section class="container-2">
    <div class="contain-header">
        <p class="upcoming-text">Upcoming Event</p>
        <h1>Featured Events</h1>
    </div>

    <div class="events-grid">
        <?php foreach ($events as $event): 
            $totalReserved = $event['total_reserved'] ?? 0;
            $remainingTickets = max(0, $event['capSalle'] - $totalReserved);
            ?>
            <div class="event-card">
                <img class="event-image" src="<?php echo $event['event_image']; ?>" alt="Event Image">
                <div class="event-details">
                    <h3 class="event-title"><?php echo $event['event_title']; ?></h3>
                    <div class="event-info">
                        <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9IiM2NjY2NjYiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIj48cmVjdCB4PSIzIiB5PSI0IiB3aWR0aD0iMTgiIGhlaWdodD0iMTgiIHJ4PSIyIiByeT0iMiI+PC9yZWN0PjxsaW5lIHgxPSIxNiIgeTE9IjIiIHgyPSIxNiIgeTI9IjYiPjwvbGluZT48bGluZSB4MT0iOCIgeTE9IjIiIHgyPSI4IiB5Mj0iNiI+PC9saW5lPjxsaW5lIHgxPSIzIiB5MT0iMTAiIHgyPSIyMSIgeTI9IjEwIj48L2xpbmU+PC9zdmc+" alt="Calendar">
                        <span><?php echo date('d M Y', strtotime($event['edition_date'])); ?>
                            <?php echo date('H:i', strtotime($event['edition_time'])); ?></span>
                    </div>
                    <div class="event-organizer">
                        <div>
                            <div class="organizer-text">Type :</div>
                            <div class="vendor-name"><?php echo $event['typeEvent']; ?></div>
                        </div>
                        <?php if ($remainingTickets <= 0): ?>
                    <img src="img/soldout.png" alt="" class="soldout">
                    <a href="event.php?eventId=<?php echo $event['eventId']; ?>&editionId=<?php echo $event['editionId']; ?>" class="buy-now-btn-soldout">BUY NOW</a>
                    <?php else:?>
                        <a href="event.php?eventId=<?php echo $event['eventId']; ?>&editionId=<?php echo $event['editionId']; ?>" class="buy-now-btn">BUY NOW</a>
                    <?php endif; ?>    

                </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

</body>

</html>