<!DOCTYPE html>
<html>
<head>
    <link href="/assets/css/app.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <script>
    Swal.fire({
        icon: 'error',
        title: "Accès interdit",
        text: "Droits insuffisants",
        timer: 3000,  // 3s
        timerProgressBar: true
    }).then(() => {
        window.location.href = '/dashboard';  // Redirection auto
    });
    </script>
</body>
</html>
