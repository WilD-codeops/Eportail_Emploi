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
        text: "Droits insuffisants pour acceder à cette page.",
        timer: 2500,  // 2.5s
        timerProgressBar: true
    }).then(() => {
        window.location.href = '/';  // Redirection auto
    });
    </script>
</body>
</html>
