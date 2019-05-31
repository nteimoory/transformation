<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Coordinate conversion</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body {
            background-image: url("img/map.jpg")
        }

        .title {
            font-weight: 600;
            color: #fffdff;
            text-shadow: 6px 1px 26px #000000;
        }
    </style>
</head>
<body>
<div class="container">

    <div class="row">
        <div style="text-align:center">
            <br>
            <br>
            <br>
            <h1 class="title">Coordinate conversion</h1>

            <br>
            <br>
            <br>
            <br>
            <br>
        </div>
    </div>
    <section class="row">

        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>Convert To Lambert</h4>
                </div>
                <div class="panel-body">
                    <form action="lambert.php" method="GET">
                        <div class="col-md-12">
                            <div class="form-group ">
                                <label for="utm_x">UTM ( x )</label>
                                <input type="text" name="utm_x" class="form-control" id="utm_x" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group ">
                                <label for="utm_y">UTM ( y )</label>
                                <input type="text" name="utm_y" class="form-control" id="utm_y" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group ">
                                <label for="zone">Zone</label>
                                <input type="text" name="zone" class="form-control" id="zone" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success btn-block">Convert To Lambert</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>Convert To WPS</h4>
                </div>
                <div class="panel-body">
                    <form action="wps.php" method="GET">
                        <input type="hidden" name="request" value="Execute"/>
                        <input type="hidden" name="service" value="wps"/>
                        <div class="col-md-12">
                            <div class="form-group ">
                                <label for="utm_x">UTM ( x )</label>
                                <input type="text" name="utm_x" class="form-control" id="utm_x" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group ">
                                <label for="utm_y">UTM ( y )</label>
                                <input type="text" name="utm_y" class="form-control" id="utm_y" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group ">
                                <label for="zone">Zone</label>
                                <input type="text" name="zone" class="form-control" id="zone" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success btn-block">Convert To WPS</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </section>
</div>


<script src="js/jquery-1.12.4.min.js"></script>
<script src="js/bootstrap.min.js"></script>

</body>
</html>

