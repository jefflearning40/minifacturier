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
# FONCTION TEST DOCKER AVEC TIMEOUT
# =========================================================
function Test-DockerReady {
    $timeout = 10

    for ($i = 0; $i -lt $timeout; $i++) {
        try {
            docker info > $null 2>&1
            return $true
        } catch {
            Start-Sleep -Seconds 1
        }
    }

    return $false
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

    $statusLabel.Text = "Statut : CHECKING DOCKER..."
    $statusLabel.ForeColor = [System.Drawing.Color]::Orange
    $form.Refresh()

    if (-not (Test-DockerReady)) {
        [System.Windows.Forms.MessageBox]::Show(
            "Docker Desktop n'est pas lancé ou son moteur Linux n'est pas prêt.",
            "Docker requis",
            [System.Windows.Forms.MessageBoxButtons]::OK,
            [System.Windows.Forms.MessageBoxIcon]::Error
        )

        $statusLabel.Text = "Statut : DOCKER NOT READY"
        $statusLabel.ForeColor = [System.Drawing.Color]::Red
        $form.Refresh()
        return
    }

    # GOTENBERG
    $statusLabel.Text = "Statut : STARTING GOTENBERG..."
    $statusLabel.ForeColor = [System.Drawing.Color]::Orange
    $form.Refresh()

    $gotenbergRunning = docker ps -q --filter name=gotenberg

    if (-not $gotenbergRunning) {
        $gotenbergExists = docker ps -aq --filter name=gotenberg

        if ($gotenbergExists) {
            docker start gotenberg
        } else {
            docker run -d --name gotenberg -p 3000:3000 gotenberg/gotenberg:8
        }
    }

    $gotenbergReady = $false
    for ($i = 0; $i -lt 15; $i++) {
        if (Test-Port 3000) {
            $gotenbergReady = $true
            break
        }
        Start-Sleep -Seconds 1
    }

    if (-not $gotenbergReady) {
        [System.Windows.Forms.MessageBox]::Show(
            "Gotenberg n'a pas démarré correctement sur le port 3000.",
            "Erreur Gotenberg",
            [System.Windows.Forms.MessageBoxButtons]::OK,
            [System.Windows.Forms.MessageBoxIcon]::Error
        )

        $statusLabel.Text = "Statut : GOTENBERG ERROR"
        $statusLabel.ForeColor = [System.Drawing.Color]::Red
        $form.Refresh()
        return
    }

    # MAILPIT
    $statusLabel.Text = "Statut : STARTING MAILPIT..."
    $statusLabel.ForeColor = [System.Drawing.Color]::Orange
    $form.Refresh()

    $mailpitRunning = docker ps -q --filter name=mailpit

    if (-not $mailpitRunning) {
        $mailpitExists = docker ps -aq --filter name=mailpit

        if ($mailpitExists) {
            docker start mailpit
        } else {
            docker run -d --name mailpit -p 8026:8025 -p 1025:1025 axllent/mailpit
        }
    }

    $mailpitReady = $false
    for ($i = 0; $i -lt 15; $i++) {
        if (Test-Port 1025) {
            $mailpitReady = $true
            break
        }
        Start-Sleep -Seconds 1
    }

    if (-not $mailpitReady) {
        [System.Windows.Forms.MessageBox]::Show(
            "Mailpit n'a pas démarré correctement sur le port 1025.",
            "Erreur Mailpit",
            [System.Windows.Forms.MessageBoxButtons]::OK,
            [System.Windows.Forms.MessageBoxIcon]::Error
        )

        $statusLabel.Text = "Statut : MAILPIT ERROR"
        $statusLabel.ForeColor = [System.Drawing.Color]::Red
        $form.Refresh()
        return
    }

    # SYMFONY
    $statusLabel.Text = "Statut : STARTING SYMFONY..."
    $statusLabel.ForeColor = [System.Drawing.Color]::Orange
    $form.Refresh()

    Start-Process powershell -WindowStyle Hidden `
    -ArgumentList "-Command", "cd '$projectPath'; symfony server:start"

    Start-Sleep -Seconds 3

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

    docker stop gotenberg 2>$null
    docker stop mailpit 2>$null

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