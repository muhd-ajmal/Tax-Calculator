<?php
require 'config.php';

function calculate_tax($salary, $start_income, $end_income, $tax_rate)
{
    /*formula for finding the tax amount: min(salary-start_income, end_income-start_income)
    until the start_income less than or equal to given salary.
    */
    $part_1 = $salary - $start_income;
    $part_2 = $end_income - $start_income;
    $tax_amount = min($part_1, $part_2) * ($tax_rate / 100);
    return $tax_amount;
}

$tax_amount = 0;

if (isset($_POST['calculate'])) {
    $salary = floatval($_POST['salary']);
    $age = $_POST['age'];
    $conn = connect();
    $query = "SELECT * FROM admin_income WHERE age < $age AND age = (SELECT MAX(age) FROM admin_income WHERE age < $age) ORDER BY age DESC";
    $sql = mysqli_execute_query($conn, $query);

    while ($result = mysqli_fetch_assoc($sql)) {
        if (isset($result['start-incom'])) {
            break;
        }
        if ($result['start_incom'] < $salary) {
            $tax_amount += calculate_tax($salary, $result['start_incom'], $result['end_incom'], $result['percentage']);
        }
    }

    mysqli_close($conn);
}

if (isset($_POST['add_tax'])) {
    $age = $_POST['age'];
    $start_incom = $_POST['start_incom'];
    $end_incom = $_POST['end_incom'];
    $tax_perc = $_POST['tax_perc'];
    $conn = connect();
    /* In the admin_income table, you won't use auto_increment contstraint, 
    that why im using sub query for getting the last id.*/
    
    $query = "INSERT INTO admin_income SELECT MAX(income_id)+1,
        $age, $start_incom, $end_incom, $tax_perc FROM admin_income
    ";
    $sql = mysqli_execute_query($conn, $query);

    if ($sql){
        echo "<script>alert('New tax added!')</script>";
    } else {
        echo "<script>alert('Failed to add tax!')</script>";;
    }


}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Calculator</title>
    <link href="assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
</head>

<body>
    <nav class="sidebar">
        <a href="#" class="active" data-target="#calc_div">
            <i class="fa-solid fa-calculator"></i>
            Calculate Tax
        </a>
        <a href="#" data-target="#tax_div"><i class="fa-solid fa-plus"></i>
            Add New Tax
        </a>
    </nav>
    <div class="content mt-5">
        <h1 class="mb-2">Tax Calculator</h1>
        <div class="container-fluid">
            <div id="calc_div" class="hide">
                <div class="div-container">
                    <form method="POST" action="index.php">
                        <div class="form-group mb-3">
                            <label for="salary">Annual Salary</label>
                            <input type="number" name="salary" class="form-control" placeholder="₹ 99,99,999" pattern="[0-9]+" title="Only accepts numerical digits!" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="salary">Employee Age</label>
                            <input type="number" name="age" class="form-control" placeholder="Age" pattern="[0-9]+" title="Only accepts numerical digits!" required>
                        </div>
                        <button class="btn btn-secondary" name="calculate">Calculate</button>
                    </form>
                    <p class="amount">Taxable Amount: ₹<?php echo $tax_amount; ?></p>
                </div>
            </div>
            <div id="tax_div" class="hide" style="display: none">
                <form method="POST" action="index.php">
                    <div class="form-group mb-3">
                    <label for="age">Employee Age</label>
                        <input type="number" name="age" class="form-control" placeholder="Age" pattern="[0-9]+" title="Only accepts numerical digits!" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="start_incom">Income Starting Range</label>
                        <input type="number" name="start_incom" class="form-control" placeholder="₹99,99,999" pattern="[0-9]+" title="Only accepts numerical digits!" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="end_incom">Income Ending Range</label>
                        <input type="number" name="end_incom" class="form-control" placeholder="₹99,99,999" pattern="[0-9]+" title="Only accepts numerical digits!" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="tax_perc">Tax Percentage</label>
                        <input type="number" name="tax_perc" class="form-control" placeholder="%" pattern="[0-9]+" title="Only accepts numerical digits!" required>
                    </div>
                    <button class="btn btn-secondary" name="add_tax">Add Tax</button>
                </form>
            </div>
        </div>


    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('a').click(function() {
                $('a').removeClass('active');
                $(this).addClass('active');
                $('.hide').hide();
                $($(this).data('target')).show()
            });
        });
    </script>
</body>

</html>