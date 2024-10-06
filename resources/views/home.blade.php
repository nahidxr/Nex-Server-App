<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Monitor</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.7/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">



    <!-- Icon for the tab -->
    <link rel="icon" href="{{ asset('/admin/dist/img/N.png') }}" type="image/png">

    <style>
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }


        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px;
            background-color: #09213a;
            color: #ffffff;
        }

        h1 {
            margin: 0;
            font-size: 24px;
        }

        .logo img {
            width: 120px;
            height: auto;
        }

        /* Updated table styles */
 
        .table thead th {
            position: sticky;
            top: 0;
            z-index: 10; /* Adjust z-index if you have overlapping elements */
            background-color: #fff; /* Ensure the header has a background */
        }

    #dataTable th {
        text-align: center;
        vertical-align: middle;
        font-size: 14px;
    }
    #dataTable td {
        text-align: center;
        vertical-align: middle;
        font-size: 14px;
    }


        .up-icon,
        .down-icon {
            font-size: 18px;
            vertical-align: middle;
        }

        .up-icon {
            color: #28a745;
        }

        .down-icon {
            color: #dc3545;
        }

        .usage-box {
            padding: 5px;
            border-radius: 5px;
        }

        ul.d-flex {
            list-style: none;
            padding-left: 0;
        }

        li.mr-3 {
            margin-right: 15px;
        }

        .text-danger {
            color: #dc3545 !important;
        }
        

  
        
        
    </style>
</head>

<body>



    <nav class="navbar navbar-expand-lg " style="background-color: #230324; color: white; "> <!-- Change #ff5733 to your desired color -->
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('/backend/assets/images/logo/nexdecade_logo.png') }}" class="brand-image img-circle elevation-3" style="opacity: .8; width: 115px;" alt="Nexdecade Logo">
            </a>
            <h1 class="navbar-text mx-auto text-white">
                <i class="fas fa-chart-bar"></i> <!-- You can change the icon class as needed -->
                Server Statistics
            </h1>
            <form method="POST" action="{{ route('logout') }}" class="d-flex">
                @csrf
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </div>
    </nav>
    
    <div class="container-fluid mt-2">
        <div class="row justify-content-center">
            <div class="col-lg-12"> <!-- Adjust column size as needed -->
                {{-- <div class="card shadow"> --}}
                <div>
                    <div class="card-body">
                        <table id="dataTable" class="table table-bordered table-hover table-striped table-head-fixed">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th>ID</th>
                                    <th width="10%">Server Name</th>
                                    <th>CPU Usage(%)</th>
                                    <th>RAM Usage(%)</th>
                                    <th>Disk Usage(%)</th>
                                    <th>Network Upload(Kbps)</th>
                                    <th>Network Download(kbps)</th>
                                    <th>UpTime</th>
                                    <th>Last Updated Time </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($servers as $server)
                                    @php
                                        $data = json_decode($server->server_data, true);
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $loop->index+1 }}</td>
                                        <td>{{ $server->server_name }}</td>
                                        <td>
                                            <div class="usage-box d-flex justify-content-center align-items-center" style="background-color: rgba(76, 175, 80, 0.2);">
                                                <span>{{ $data['cpu_usage'] ?? 0 }}%</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="usage-box d-flex justify-content-center align-items-center" style="background-color: rgba(3, 169, 244, 0.2);">
                                                <span>{{ $data['ram_usage'] ?? 0 }}%</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="usage-box d-flex justify-content-center align-items-center" style="background-color: rgba(189, 189, 189, 0.2);">
                                                <span>{{ $data['disk_usage'] ?? 0 }}%</span>
                                            </div>
                                        </td>
                                        <td>{{ number_format(($data['network_upload'] / 1024) * 8, 2) }} kbps</td>
                                        <td>{{ number_format(($data['network_download'] / 1024) * 8, 2) }} kbps</td>
                                        <td>
                                            @php
                                                $uptimeInSeconds = isset($data['uptime']) ? $data['uptime'] : 0;
                                                $uptimeInMinutes = floor($uptimeInSeconds / 60);
                                                $uptimeInHours = floor($uptimeInMinutes / 60);
                                                $uptimeInDays = floor($uptimeInHours / 24);
                                                $uptimeInMonths = floor($uptimeInDays / 30);
                                                $uptimeInYears = floor($uptimeInMonths / 12);
    
                                                $remainingDays = $uptimeInDays % 30;
                                                $remainingHours = $uptimeInHours % 24;
                                                $remainingMinutes = $uptimeInMinutes % 60;
    
                                                $uptimeParts = [];
    
                                                if ($uptimeInYears > 0) {
                                                    $uptimeParts[] = "{$uptimeInYears} year" . ($uptimeInYears > 1 ? 's' : '');
                                                }
                                                if ($remainingDays > 0) {
                                                    $uptimeParts[] = "{$remainingDays} day" . ($remainingDays > 1 ? 's' : '');
                                                }
                                                if ($remainingHours > 0) {
                                                    $uptimeParts[] = "{$remainingHours} hour" . ($remainingHours > 1 ? 's' : '');
                                                }
                                                if ($remainingMinutes > 0) {
                                                    $uptimeParts[] = "{$remainingMinutes} minute" . ($remainingMinutes > 1 ? 's' : '');
                                                }
    
                                                $uptimeString = implode(' ', $uptimeParts) ?: '-';
                                            @endphp
    
                                            <span>{{ $uptimeString }}</span>
                                        </td>
                                        <td>{{ $server->updated_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- <footer class="bg-dark text-white text-center py-4">
        <div class="container">
            <p class="mb-0">
                © Copyright 2024. All rights reserved. 
                <a href="https://nexdecade.com/" target="_blank" class="text-white" style="text-decoration: underline;">
                    Nexdecade Technology Pvt. LTD
                </a>.
            </p>
        </div>
    </footer> --}}
  
    
    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.7/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.7/js/dataTables.bootstrap5.js"></script>


    <script>

        new DataTable('#dataTable', {
            fixedHeader: true,
            info: false,     
            ordering: true,
            paging: false,      
            searching: true,  
        });


    </script>
    <script>
        // Refresh the page every 5 minutes (300000 milliseconds)
        setInterval(function() {
            location.reload();
        }, 300000); // 5 minutes in milliseconds
    </script>

</body>


</html>
