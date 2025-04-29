<?php
include 'db.php';

// Fetch only active users (Soft delete functionality)
$sql = "SELECT * FROM users WHERE deleted = 0 ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

// Debugging
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>

    <!-- Bootstrap CSS (Latest) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables CSS (Latest) -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css"/>

    <!-- jQuery (Latest) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- DataTables JS (Latest) -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#userTable').DataTable({
                "paging": true,        // Enable pagination
                "searching": true,     // Enable search box
                "lengthChange": true,  // Allow user to choose how many entries to show
                "ordering": true,      // Enable sorting
                "info": true           // Show table info
            });
        });
    </script>

</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center">User List</h2>
        
        <table id="userTable" class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th> <!-- Corrected ID numbering -->
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Gender</th>
                    <th>Mobile</th>
                    <th>City</th>
                    <th>Language</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $counter = 1; // Initialize row number
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                            <td>{$counter}</td> <!-- Corrected ID numbering -->
                            <td>{$row['firstName']}</td>
                            <td>{$row['lastName']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['gender']}</td>
                            <td>{$row['mobile']}</td>
                            <td>{$row['city']}</td>
                            <td>{$row['language']}</td>
                            
                        </tr>";
                        $counter++; // Increment row number
                    }
                } else {
                    echo "<tr><td colspan='9' class='text-center'>No users found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS (Latest) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
