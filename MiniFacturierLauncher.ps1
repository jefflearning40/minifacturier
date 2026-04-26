#encodage UTF-8 pour affichage des termes boutons et commentaires status
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8
# On charge les bibliothèques Windows Forms
Add-Type -AssemblyName System.Windows.Forms
Add-Type -AssemblyName System.Drawing

# Chemin du projet
$projectPath = "C:\laragon\www\minifacturier"

# Création de la fenêtre principale
$form = New-Object System.Windows.Forms.Form
$form.Text = "MiniFacturier"
$form.Width = 320
$form.Height = 220
$form.StartPosition = "CenterScreen"

# Texte de statut
$statusLabel = New-Object System.Windows.Forms.Label
$statusLabel.Text = "Statut : CLOSED"
$statusLabel.Width = 260
$statusLabel.Height = 30
$statusLabel.Left = 30
$statusLabel.Top = 130
$statusLabel.TextAlign = "MiddleCenter"
$statusLabel.ForeColor = [System.Drawing.Color]::Red

# Bouton ON
$buttonOn = New-Object System.Windows.Forms.Button
$buttonOn.Text = "ON"
$buttonOn.Width = 220
$buttonOn.Height = 40
$buttonOn.Left = 45
$buttonOn.Top = 25

$buttonOn.Add_Click({

    $statusLabel.Text = "Statut : STARTING..."
    $statusLabel.ForeColor = [System.Drawing.Color]::Orange

    # Lance Docker Desktop si besoin
    #Start-Process "C:\Program Files\Docker\Docker\Docker Desktop.exe" -ErrorAction SilentlyContinue

    # Attend que Docker soit prêt
    do {
        Start-Sleep -Seconds 2
        $dockerReady = docker info 2>$null
    } until ($dockerReady)

    # Vérifie si Gotenberg tourne déjà
    $gotenbergRunning = docker ps -q --filter ancestor=gotenberg/gotenberg:8

    if (-not $gotenbergRunning) {
        Start-Process powershell -WindowStyle Hidden -ArgumentList "-Command", "cd $projectPath; docker run --rm -p 3000:3000 gotenberg/gotenberg:8"
    }

    # Lance Symfony
    Start-Process powershell -WindowStyle Hidden -ArgumentList "-Command", "cd $projectPath; symfony server:start"

    # Petite pause pour laisser Symfony démarrer
    Start-Sleep -Seconds 3

    # Ouvre le navigateur
    Start-Process "http://127.0.0.1:8000"

    $statusLabel.Text = "Statut :APP LAUNCHED"
    $statusLabel.ForeColor = [System.Drawing.Color]::Green
})

# Bouton OFF
$buttonOff = New-Object System.Windows.Forms.Button
$buttonOff.Text = "OFF"
$buttonOff.Width = 220
$buttonOff.Height = 40
$buttonOff.Left = 45
$buttonOff.Top = 80

$buttonOff.Add_Click({

    $statusLabel.Text = "Statut :APP CLOSED"
    $statusLabel.ForeColor = [System.Drawing.Color]::Orange

    # Arrête Symfony
    Start-Process powershell -WindowStyle Hidden -ArgumentList "-Command", "cd $projectPath; symfony server:stop"

    # Arrête Gotenberg
    Start-Process powershell -WindowStyle Hidden -ArgumentList "-Command", "docker ps -q --filter ancestor=gotenberg/gotenberg:8 | ForEach-Object { docker stop $_ }"

    Start-Sleep -Seconds 2

    $statusLabel.Text = "Statut :APP CLOSED"
    $statusLabel.ForeColor = [System.Drawing.Color]::Red
})

# Ajout des éléments
$form.Controls.Add($buttonOn)
$form.Controls.Add($buttonOff)
$form.Controls.Add($statusLabel)

# Affiche la fenêtre
$form.ShowDialog()