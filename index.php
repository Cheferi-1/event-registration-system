<?php

$host = "localhost";
$port = 3307;
$dbname = "lab05_events";
$username = "root";
$password = "";

$errors = [];
$success = "";

$participant_name = "";
$email = "";
$phone = "";
$workshop = "";
$attendance_mode = "";
$number_of_seats = "";
$special_requirements = "";
$accept_terms = "";

try {
    // Connect to database
    $conn = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Database connection failed. Check your database settings.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Collect form values
    $participant_name = trim($_POST["participant_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $workshop = trim($_POST["workshop"] ?? "");
    $attendance_mode = trim($_POST["attendance_mode"] ?? "");
    $number_of_seats = trim($_POST["number_of_seats"] ?? "");
    $special_requirements = trim($_POST["special_requirements"] ?? "");
    $accept_terms = isset($_POST["accept_terms"]) ? "1" : "";

    // Available workshops
    $workshops = [
        "Web Development",
        "Cybersecurity",
        "Artificial Intelligence",
        "Digital Marketing",
        "Entrepreneurship"
    ];

    // Available attendance modes
    $attendance_modes = [
        "Physical",
        "Online"
    ];

    // Validate participant name
    if ($participant_name == "") {
        $errors[] = "Participant name is required.";
    }

    // Validate email
    if ($email == "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    // Validate workshop
    if (!in_array($workshop, $workshops, true)) {
        $errors[] = "Please select a valid workshop.";
    }

    // Validate attendance mode
    if (!in_array($attendance_mode, $attendance_modes, true)) {
        $errors[] = "Please select an attendance mode.";
    }

    // Validate number of seats
    if (
        !preg_match('/^[1-3]$/', $number_of_seats)
    ) {
        $errors[] = "Number of seats must be between 1 and 3.";
    }

    // Validate special requirements
    if (strlen($special_requirements) > 300) {
        $errors[] = "Special requirements cannot exceed 300 characters.";
    }

    // Validate terms
    if ($accept_terms != "1") {
        $errors[] = "You must accept the event terms.";
    }

    // Save registration if valid
    if (empty($errors)) {

        try {

            // Check for duplicate registration
            $check = $conn->prepare(
                "SELECT id FROM registrations
                 WHERE email = ? AND workshop = ?"
            );

            $check->execute([$email, $workshop]);

            if ($check->fetch()) {

                $errors[] = "This email is already registered for this workshop.";

            } else {

                // Insert registration
                $sql = "INSERT INTO registrations
                (participant_name, email, phone, workshop,
                attendance_mode, number_of_seats,
                special_requirements, accept_terms)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

                $stmt = $conn->prepare($sql);

                $stmt->execute([
                    $participant_name,
                    $email,
                    $phone,
                    $workshop,
                    $attendance_mode,
                    $number_of_seats,
                    $special_requirements,
                    1
                ]);

                $registration_id = $conn->lastInsertId();

                $success = "Registration successful! Your registration reference is #" . $registration_id;

                // Clear form after success
                $participant_name = "";
                $email = "";
                $phone = "";
                $workshop = "";
                $attendance_mode = "";
                $number_of_seats = "";
                $special_requirements = "";
                $accept_terms = "";
            }

        } catch (PDOException $e) {

            if ($e->getCode() == "23000") {
                $errors[] = "This email is already registered for this workshop.";
            } else {
                $errors[] = "Could not save registration. Please try again.";
            }
        }
    }
}

// Get registration totals for each workshop
$totals = $conn->query(
    "SELECT workshop,
            COUNT(*) AS total_registrations,
            SUM(number_of_seats) AS total_seats
     FROM registrations
     GROUP BY workshop
     ORDER BY workshop ASC"
)->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Event Registration</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <h1>Event Registration</h1>

    <p class="subtitle">Register for your preferred workshop.</p>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <strong>Please correct these errors:</strong>

            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success != ""): ?>
        <div class="success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">

        <label>Participant Name *</label>
        <input type="text" name="participant_name"
            value="<?= htmlspecialchars($participant_name) ?>" required>

        <label>Email Address *</label>
        <input type="email" name="email"
            value="<?= htmlspecialchars($email) ?>" required>

        <label>Phone Number</label>
        <input type="tel" name="phone"
            value="<?= htmlspecialchars($phone) ?>">

        <label>Workshop *</label>
        <select name="workshop" required>

            <option value="">Select Workshop</option>

            <?php
            $workshops = [
                "Web Development",
                "Cybersecurity",
                "Artificial Intelligence",
                "Digital Marketing",
                "Entrepreneurship"
            ];

            foreach ($workshops as $item):
            ?>

                <option value="<?= htmlspecialchars($item) ?>"
                    <?= $workshop == $item ? "selected" : "" ?>>
                    <?= htmlspecialchars($item) ?>
                </option>

            <?php endforeach; ?>

        </select>

        <label>Attendance Mode *</label>
        <select name="attendance_mode" required>

            <option value="">Select Attendance Mode</option>

            <option value="Physical"
                <?= $attendance_mode == "Physical" ? "selected" : "" ?>>
                Physical
            </option>

            <option value="Online"
                <?= $attendance_mode == "Online" ? "selected" : "" ?>>
                Online
            </option>

        </select>

        <label>Number of Seats *</label>
        <select name="number_of_seats" required>

            <option value="">Select Number of Seats</option>

            <option value="1" <?= $number_of_seats == "1" ? "selected" : "" ?>>1</option>
            <option value="2" <?= $number_of_seats == "2" ? "selected" : "" ?>>2</option>
            <option value="3" <?= $number_of_seats == "3" ? "selected" : "" ?>>3</option>

        </select>

        <label>Special Requirements</label>
        <textarea name="special_requirements"
            maxlength="300" rows="4"><?= htmlspecialchars($special_requirements) ?></textarea>

        <div class="checkbox">

            <input type="checkbox" name="accept_terms" value="1"
                <?= $accept_terms == "1" ? "checked" : "" ?>>

            <label>I accept the event terms and conditions. *</label>

        </div>

        <button type="submit">Register Now</button>

    </form>

</div>

<div class="inventory">

    <h2>Workshop Registration Summary</h2>

    <?php if (empty($totals)): ?>

        <p>No registrations available yet.</p>

    <?php else: ?>

        <div class="table-container">

            <table>

                <thead>
                    <tr>
                        <th>Workshop</th>
                        <th>Total Registrations</th>
                        <th>Total Seats</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($totals as $row): ?>

                        <tr>
                            <td><?= htmlspecialchars($row["workshop"]) ?></td>
                            <td><?= (int)$row["total_registrations"] ?></td>
                            <td><?= (int)$row["total_seats"] ?></td>
                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</div>

</body>
</html>