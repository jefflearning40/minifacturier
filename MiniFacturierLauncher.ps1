# =========================================================
# ENCODAGE UTF-8
# =========================================================
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8

# =========================================================
# IMPORT DES LIBRAIRIES POUR L'INTERFACE GRAPHIQUE
# =========================================================
Add-Type -AssemblyName System.Windows.Forms
Add-Type -AssemblyName System.Drawing

# =========================================================
# CHEMIN DU PROJET
# =========================================================
$projectPath = "C:\laragon\www\minifacturier"

# =========================================================
# FONCTION TEST PORT
# =========================================================
function Test-Port {
    param ($port)

    try {
        $client = New-Object System.Net.Sockets.TcpClient
        $client.Connect("127.0.0.1", $port)
        $client.Close()
        return $true
    } catch {
        return $false
    }
}

# =========================================================
# FONCTION UPDATE LARAGON
# =========================================================
function Update-LaragonStatus {
    if (Test-Port 80 -or Test-Port 3306) {
        $infoLabel.Text = "LARAGON READY"
        $infoLabel.BackColor = [System.Drawing.Color]::Green
    } else {
        $infoLabel.Text = "LANCER LARAGON MANUELLEMENT"
        $infoLabel.BackColor = [System.Drawing.Color]::Crimson
    }
}

# =========================================================
# CRÉATION DE LA FENÊTRE PRINCIPALE
# =========================================================
$form = New-Object System.Windows.Forms.Form
$form.Text = "MiniFacturier"
$form.Width = 340
$form.Height = 260
$form.StartPosition = "CenterScreen"

# =========================================================
# ICÔNE PERSONNALISÉE
# =========================================================
$iconPath = "C:\laragon\www\minifacturier\documentation\minifacturier.ico"

if (Test-Path $iconPath) {
    $form.Icon = New-Object System.Drawing.Icon($iconPath)
}

# =========================================================
# MESSAGE LARAGON
# =========================================================
$infoLabel = New-Object System.Windows.Forms.Label
$infoLabel.Width = 300
$infoLabel.Height = 40
$infoLabel.Left = 20
$infoLabel.Top = 10
$infoLabel.TextAlign = "MiddleCenter"
$infoLabel.Font = New-Object System.Drawing.Font("Segoe UI", 10, [System.Drawing.FontStyle]::Bold)
$infoLabel.ForeColor = [System.Drawing.Color]::White

Update-LaragonStatus

# =========================================================
# TIMER AUTO UPDATE LARAGON
# =========================================================
$laragonTimer = New-Object System.Windows.Forms.Timer
$laragonTimer.Interval = 1000
$laragonTimer.Add_Tick({
    Update-LaragonStatus
})
$laragonTimer.Start()

# =========================================================
# LABEL STATUT
# =========================================================
$statusLabel = New-Object System.Windows.Forms.Label
$statusLabel.Text = "Statut : CLOSED"
$statusLabel.Width = 260
$statusLabel.Height = 30
$statusLabel.Left = 40
$statusLabel.Top = 180
$statusLabel.TextAlign = "MiddleCenter"
$statusLabel.ForeColor = [System.Drawing.Color]::Red

# =========================================================
# BOUTON ON
# =========================================================
$buttonOn = New-Object System.Windows.Forms.Button
$buttonOn.Text = "ON"
$buttonOn.Width = 220
$buttonOn.Height = 40
$buttonOn.Left = 50
$buttonOn.Top = 60

$buttonOn.Add_Click({

    if (-not (Test-Port 80 -or Test-Port 3306)) {
        [System.Windows.Forms.MessageBox]::Show(
            "Laragon n'est pas lancé. Lance Laragon avant de démarrer l'application.",
            "Laragon requis",
            [System.Windows.Forms.MessageBoxButtons]::OK,
            [System.Windows.Forms.MessageBoxIcon]::Warning
        )
        return
    }

    $statusLabel.Text = "Statut : STARTING..."
    $statusLabel.ForeColor = [System.Drawing.Color]::Orange
    $form.Refresh()

    # ATTENTE DOCKER
    do {
        Start-Sleep -Seconds 2
        $dockerReady = docker info 2>$null
    } until ($dockerReady)

    # GOTENBERG
    $gotenbergRunning = docker ps -q --filter name=gotenberg

    if (-not $gotenbergRunning) {
        $gotenbergExists = docker ps -aq --filter name=gotenberg

        if ($gotenbergExists) {
            docker start gotenberg
        } else {
            docker run -d --name gotenberg -p 3000:3000 gotenberg/gotenberg:8
        }
    }

    # MAILPIT
    $mailpitRunning = docker ps -q --filter name=mailpit

    if (-not $mailpitRunning) {
        $mailpitExists = docker ps -aq --filter name=mailpit

        if ($mailpitExists) {
            docker start mailpit
        } else {
            docker run -d --name mailpit -p 8026:8025 -p 1025:1025 axllent/mailpit
        }
    }

    # SYMFONY
    Start-Process powershell -WindowStyle Hidden `
    -ArgumentList "-Command", "cd '$projectPath'; symfony server:start"

    Start-Sleep -Seconds 3

    # OUVERTURE APP UNIQUEMENT
    Start-Process "http://127.0.0.1:8000"

    $statusLabel.Text = "Statut : APP LAUNCHED"
    $statusLabel.ForeColor = [System.Drawing.Color]::Green
    $form.Refresh()
})

# =========================================================
# BOUTON OFF
# =========================================================
$buttonOff = New-Object System.Windows.Forms.Button
$buttonOff.Text = "OFF"
$buttonOff.Width = 220
$buttonOff.Height = 40
$buttonOff.Left = 50
$buttonOff.Top = 110

$buttonOff.Add_Click({

    $statusLabel.Text = "Statut : CLOSING..."
    $statusLabel.ForeColor = [System.Drawing.Color]::Orange
    $form.Refresh()

    for ($i = 1; $i -le 3; $i++) {
        $points = "." * $i
        $statusLabel.Text = "Statut : CLOSING$points"
        $form.Refresh()
        Start-Sleep -Milliseconds 500
    }

    Start-Process powershell -WindowStyle Hidden `
    -ArgumentList "-Command", "cd '$projectPath'; symfony server:stop"

    docker stop gotenberg
    docker stop mailpit

    Start-Sleep -Seconds 2

    $statusLabel.Text = "Statut : APP CLOSED"
    $statusLabel.ForeColor = [System.Drawing.Color]::Red
    $form.Refresh()

    Start-Sleep -Seconds 2
    $form.Close()
})

# =========================================================
# AJOUT ELEMENTS
# =========================================================
$form.Controls.Add($infoLabel)
$form.Controls.Add($buttonOn)
$form.Controls.Add($buttonOff)
$form.Controls.Add($statusLabel)

# =========================================================
# ARRÊT PROPRE DU TIMER À LA FERMETURE
# =========================================================
$form.Add_FormClosed({
    $laragonTimer.Stop()
})

$form.ShowDialog()