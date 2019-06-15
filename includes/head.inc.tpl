<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>[@pagetitle]</title>
    <!-- Bootstrap Core CSS -->
    <link href="../resourses/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="../resourses/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../resourses/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
				<a class="navbar-brand" href="index.php">Rally Manager</a>
				</div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">
				<li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="true">
                        <i class="fa fa-th-list"></i>&nbsp; [@sitetitle] <i class="fa fa-caret-down"></i>
                    </a>
					<ul class="dropdown-menu dropdown-alerts">
                    [foreach @rally]
                        <li>
                            <a href="includes/change_rally.php?rally_id=[@rallyid]">
                                <div>
                                    <i class="fa fa-circle fa-fw"></i> [@rallytitle]
									<span class="pull-right text-muted small">[@rallydate]</span>
                                </div>
                            </a>
                        </li>
					[/foreach]
						<li><a class="text-center" href="new_rally.php"><i class="fa fa-plus"></i> <strong>Create New Rally</strong></a></li>
                    </ul>
                </li>
				<li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
                        <i class="fa fa-user fa-fw"></i> [@username] <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
						<li><a href="index.php"><i class="fa fa-user fa-fw"></i> Profile</a></li>
						<li><a href="mysettings.php"><i class="fa fa-user fa-fw"></i> Settings</a></li>
                        <li><a href="logout.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li>
                            <a href="../dashboard.php"><i class="fa fa-dashboard fa-fw"></i> Rally Dashboard</a>
                        </li>
						<li>
                            <a href="../database.php"><i class="fa fa-database fa-fw"></i> Rally Database</a>
                        </li>
                        <li>
                            <a href="../register_pilot.php"><i class="fa fa-edit fa-fw"></i> Register a Pilot</a>
                        </li>
						<li>
                            <a href="../labels.php"><i class="fa fa-th fa-fw"></i> Print Pilot Labels</a>
                        </li>
                        <li>
                            <a href="../register.php"><i class="fa fa-edit fa-fw"></i> Register a Young Eagle</a>
                        </li>
						<li>
                            <a href="../print.php"><i class="fa fa-certificate fa-fw"></i> Print Certificates</a>
                        </li>
						<li>
                            <a href="../report.php"><i class="fa fa-file-text-o fa-fw"></i> Print Final Reports</a>
                        </li>
						<li>
                            <a href="../settings.php"><i class="fa fa-gear fa-fw"></i> Settings</a>
                        </li>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <!-- Page Content -->
        <div id="page-wrapper">