<?php
session_start();

require 'config.php'; 

if (!isset($_SESSION['utilisateur_name'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    echo 'Invalid request. Reservation ID is required.';
    exit();
}

$reservationId = (int) $_GET['id'];

$query = 'SELECT r.idReservation, r.qteBilletsNormal, r.qteBilletsReduit, e.eventTitle, u.nomUser, u.prenomUser, e.TariffNormal, e.TariffReduit
          FROM reservation r
          JOIN edition ed ON r.editionId = ed.editionId
          JOIN evenement e ON ed.eventId = e.eventId
          JOIN utilisateur u ON r.idUser = u.idUser
          WHERE r.idReservation = :reservationId';

$stmt = $pdo->prepare($query);
$stmt->bindParam(':reservationId', $reservationId);
$stmt->execute();
$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reservation) {
    echo 'Reservation not found.';
    exit();
}

$queryBillets = 'SELECT b.billetId, b.typeBillet, b.placeNum
                 FROM billet b
                 WHERE b.idReservation = :reservationId';

$stmtBillets = $pdo->prepare($queryBillets);
$stmtBillets->bindParam(':reservationId', $reservationId);
$stmtBillets->execute();
$billets = $stmtBillets->fetchAll(PDO::FETCH_ASSOC);


$association_name = "ASSOCIATION FARHA";
$location = "Centre Culturel Farha, Tanger";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture #<?php echo htmlspecialchars($reservation['idReservation']); ?></title>>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        
        body {
            padding: 30px;
            max-width: 800px;
            margin: 0 auto;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }
        
        .company-info {
            font-weight: bold;
        }
        
        .company-name {
            font-size: 18px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .company-address {
            font-size: 12px;
            font-weight: normal;
        }
        
        .client-info {
            text-align: right;
            font-size: 12px;
        }
        
        .client-name {
            font-weight: bold;
        }
        
        .event-info {
            text-transform: uppercase;
            margin-bottom: 30px;
        }
        
        .event-name {
            font-weight: bold;
        }
        
        .invoice-title {
            text-transform: uppercase;
            font-size: 20px;
            font-weight: bold;
            margin: 20px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        th, td {
            padding: 10px 5px;
            text-align: left;
        }
        
        th {
            border-bottom: 1px solid #ddd;
        }
        
        td {
            border-bottom: 1px solid #eee;
        }
        
        tr:last-child td {
            border-bottom: 1px solid #ddd;
        }
        
        .text-right {
            text-align: right;
        }
        
        .total-row {
            text-align: right;
            margin: 20px 0;
            font-weight: bold;
        }
        
        .total-line {
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }
        
        .thank-you {
            text-align: center;
            margin-top: 50px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .button-container {
            margin-top: 20px;
            text-align: center;
        }
        button {
            background: #007BFF;
            color: white;
            border: none;
            padding: 10px 15px;
            margin: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    
    <div id="invoice">
        <div class="header">
            <div class="company-info">
                <div class="company-name"><?php echo $association_name; ?></div>
                <div class="company-address"><?php echo $location; ?></div>
            </div>
            <div class="client-info">
                <div>Client :</div>
                <div class="client-name"><?php echo htmlspecialchars($reservation['nomUser'] . ' ' . $reservation['prenomUser']); ?></div>
                <div>Adresse email :</div>
                <div><?php echo htmlspecialchars($_SESSION['utilisateur_email'] ?? 'N/A'); ?></div>
            </div>
        </div>
        
        <div class="event-info">
            <div class="event-name"><?php echo htmlspecialchars($reservation['eventTitle']); ?></div>
            <div><?php echo date('d/m/Y à H\H\0\0'); ?></div>
        </div>
        
        <div class="invoice-title">FACTURE #<?php echo htmlspecialchars($reservation['idReservation']); ?></div>
        
        <table>
            <thead>
                <tr>
                    <th>Tarif</th>
                    <th>Prix</th>
                    <th>Qté</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Normal </td>
                    <td><?php echo number_format($reservation['TariffNormal'], 2); ?> MAD</td>
                    <td><?php echo $reservation['qteBilletsNormal']; ?></td>
                    <td class="text-right"><?php echo number_format($reservation['qteBilletsNormal'] * $reservation['TariffNormal'], 2); ?>MAD</td>
                </tr>
                <tr>
                    <td>Réduit</td>
                    <td><?php echo number_format($reservation['TariffReduit'], 2); ?> MAD</td>
                    <td><?php echo $reservation['qteBilletsReduit']; ?></td>
                    <td><?php echo number_format($reservation['qteBilletsReduit'] * $reservation['TariffReduit'], 2); ?> MAD</td>
                </tr>
                <tr>
                    <td><strong>Price Total : </strong></td>
                    <td></td>
                    <td><?=  $totalQte?></td>
                    <td class="text-right"><strong><?php 
                $totalPrice = ($reservation['qteBilletsNormal'] * $reservation['TariffNormal']) + 
                              ($reservation['qteBilletsReduit'] * $reservation['TariffReduit']);
                echo number_format($totalPrice, 2); ?></strong></td>
                </tr>
            </tbody>
        </table>
        
        <div class="thank-you">MERCI !</div>
    </div>


    <div class="button-container">
        <button onclick="downloadFacture()">Download Invoice</button>
        <form method="POST">
            <input type="hidden" name="reservation_id" value="<?php echo $reservationId; ?>">
            <button name='toMyTickets'>Show Tickets</button>
        </form>
    </div>
</body>

<script>
       function downloadFacture() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('p', 'mm', 'a4'); 

        const invoiceElement = document.getElementById("invoice"); 

        html2canvas(invoiceElement, { scale: 2 }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const imgWidth = 210; 
            const pageHeight = 297; 
            const imgHeight = (canvas.height * imgWidth) / canvas.width; 

            let position = 10;
            doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);

           
            doc.save("facture_<?php echo $invoice_number; ?>.pdf");
        });
    }


    </script>

</html>