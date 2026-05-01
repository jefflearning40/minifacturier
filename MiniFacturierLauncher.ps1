# =========================================================
# ENCODAGE UTF-8
# =========================================================
try {
    [Console]::OutputEncoding = [System.Text.Encoding]::UTF8
} catch {}

Add-Type -AssemblyName System.Windows.Forms
Add-Type -AssemblyName System.Drawing

# =========================================================
# CHEMINS PRINCIPAUX
# =========================================================
$projectPath = "C:\laragon\www\minifacturier"
$chromePath = "C:\Program Files\Google\Chrome\Application\chrome.exe"
$appUrl = "http://127.0.0.1:8000/login"

$chromeUserDataDir = "$env:TEMP\MiniFacturier_Chrome_Profile"
$script:isLaunching = $false

# =========================================================
# TEST PORT LOCAL
# =========================================================
function Test-Port {
    param ([int]$port)

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
# TEST LARAGON
# =========================================================
function Test-LaragonReady {
    return (Test-Port 80) -or (Test-Port 3306)
}

# =========================================================
# PROGRESSION
# =========================================================
function Set-Progress {
    param (
        [int]$value,
        [string]$message
    )

    if ($value -lt 0) { $value = 0 }
    if ($value -gt 100) { $value = 100 }

    $progressBar.Value = $value
    $progressLabel.Text = "$value% - $message"
    $form.Refresh()
}

# =========================================================
# STATUT LARAGON
# =========================================================
function Update-LaragonStatus {
    if (Test-LaragonReady) {
        $laragonLabel.Text = "LARAGON READY"
        $laragonLabel.BackColor = [System.Drawing.Color]::Green
        $laragonLabel.ForeColor = [System.Drawing.Color]::White

        if (-not $script:isLaunching) {
            $buttonOn.Enabled = $true
        }
    } else {
        $laragonLabel.Text = "OPEN LARAGON"
        $laragonLabel.BackColor = [System.Drawing.Color]::Crimson
        $laragonLabel.ForeColor = [System.Drawing.Color]::White
        $buttonOn.Enabled = $false
    }

    $form.Refresh()
}

# =========================================================
# DOCKER
# =========================================================
function Start-DockerDesktopSilent {
    $statusLabel.Text = "Status: STARTING DOCKER..."
    $statusLabel.ForeColor = [System.Drawing.Color]::Orange
    Set-Progress 5 "Starting Docker Desktop"

    docker desktop start > $null 2>&1
}

function Wait-Docker {
    param ([int]$timeout = 90)

    for ($i = 1; $i -le $timeout; $i++) {
        $statusLabel.Text = "Status: CHECKING DOCKER... $i/$timeout"
        $percent = 10 + [math]::Round(($i / $timeout) * 20)
        Set-Progress $percent "Checking Docker"

        docker info > $null 2>&1

        if ($LASTEXITCODE -eq 0) {
            Set-Progress 30 "Docker ready"
            return $true
        }

        Start-Sleep -Seconds 1
    }

    return $false
}

# =========================================================
# GOTENBERG HEALTH
# =========================================================
function Test-GotenbergHealth {
    try {
        $response = Invoke-WebRequest "http://127.0.0.1:3000/health" -UseBasicParsing -TimeoutSec 3
        return ($response.StatusCode -eq 200)
    } catch {
        return $false
    }
}

function Wait-Gotenberg {
    param ([int]$timeout = 40)

    for ($i = 1; $i -le $timeout; $i++) {
        $percent = 35 + [math]::Round(($i / $timeout) * 20)
        Set-Progress $percent "Checking Gotenberg"

        if (Test-GotenbergHealth) {
            Set-Progress 55 "Gotenberg ready"
            return $true
        }

        Start-Sleep -Seconds 1
    }

    return $false
}

function Start-GotenbergSafe {
    $statusLabel.Text = "Status: STARTING GOTENBERG..."
    $statusLabel.ForeColor = [System.Drawing.Color]::Orange
    Set-Progress 35 "Starting Gotenberg"

    $running = docker ps -q --filter "name=^/gotenberg$"

    if ($running) {
        return $true
    }

    $exists = docker ps -aq --filter "name=^/gotenberg$"

    if ($exists) {
        docker start gotenberg > $null 2>&1
        Start-Sleep -Seconds 3

        $stillRunning = docker ps -q --filter "name=^/gotenberg$"

        if (-not $stillRunning) {
            docker rm gotenberg > $null 2>&1
            docker run -d --restart unless-stopped --name gotenberg -p 3000:3000 gotenberg/gotenberg:8 > $null 2>&1
        }
    } else {
        docker run -d --restart unless-stopped --name gotenberg -p 3000:3000 gotenberg/gotenberg:8 > $null 2>&1
    }

    Start-Sleep -Seconds 2
    return $true
}

# =========================================================
# MAILPIT
# =========================================================
function Wait-Mailpit {
    param ([int]$timeout = 40)

    for ($i = 1; $i -le $timeout; $i++) {
        $percent = 60 + [math]::Round(($i / $timeout) * 20)
        Set-Progress $percent "Checking Mailpit"

        if (Test-Port 8025) {
            Set-Progress 80 "Mailpit ready"
            return $true
        }

        Start-Sleep -Seconds 1
    }

    return $false
}

function Start-MailpitSafe {
    $statusLabel.Text = "Status: STARTING MAILPIT..."
    $statusLabel.ForeColor = [System.Drawing.Color]::Orange
    Set-Progress 60 "Starting Mailpit"

    $running = docker ps -q --filter "name=^/mailpit$"

    if ($running) {
        return $true
    }

    $exists = docker ps -aq --filter "name=^/mailpit$"

    if ($exists) {
        docker start mailpit > $null 2>&1
        Start-Sleep -Seconds 3

        $stillRunning = docker ps -q --filter "name=^/mailpit$"

        if (-not $stillRunning) {
            docker rm mailpit > $null 2>&1
            docker run -d --restart unless-stopped --name mailpit -p 8025:8025 -p 1025:1025 axllent/mailpit > $null 2>&1
        }
    } else {
        docker run -d --restart unless-stopped --name mailpit -p 8025:8025 -p 1025:1025 axllent/mailpit > $null 2>&1
    }

    Start-Sleep -Seconds 2
    return $true
}

# =========================================================
# SYMFONY
# =========================================================
function Start-SymfonySafe {
    $statusLabel.Text = "Status: STARTING SYMFONY..."
    $statusLabel.ForeColor = [System.Drawing.Color]::Orange
    Set-Progress 90 "Starting Symfony"

    Start-Process powershell -WindowStyle Hidden `
        -ArgumentList "-Command", "cd '$projectPath'; symfony server:start --no-tls"

    for ($i = 1; $i -le 20; $i++) {
        Set-Progress (90 + [math]::Round(($i / 20) * 5)) "Checking Symfony"

        if (Test-Port 8000) {
            return $true
        }

        Start-Sleep -Seconds 1
    }

    return $false
}

function Stop-SymfonySafe {
    Start-Process powershell -WindowStyle Hidden `
        -ArgumentList "-Command", "cd '$projectPath'; symfony server:stop"
}

# =========================================================
# CHROME APP
# =========================================================
function Open-AppBrowser {
    if (-not (Test-Path $chromePath)) {
        $chromePathAlt = "C:\Program Files (x86)\Google\Chrome\Application\chrome.exe"

        if (Test-Path $chromePathAlt) {
            $script:chromePath = $chromePathAlt
        } else {
            Start-Process $appUrl
            return
        }
    }

    if (Test-Path $chromeUserDataDir) {
        Remove-Item $chromeUserDataDir -Recurse -Force -ErrorAction SilentlyContinue
    }

    New-Item -ItemType Directory -Path $chromeUserDataDir -Force | Out-Null

    Start-Process $chromePath `
        -ArgumentList "--user-data-dir=`"$chromeUserDataDir`"", "--app=$appUrl", "--start-maximized", "--no-first-run", "--disable-extensions" `
        -PassThru | Out-Null
}

function Close-AppBrowser {
    try {
        Get-CimInstance Win32_Process |
            Where-Object {
                $_.Name -eq "chrome.exe" -and
                $_.CommandLine -like "*MiniFacturier_Chrome_Profile*"
            } |
            ForEach-Object {
                Stop-Process -Id $_.ProcessId -Force -ErrorAction SilentlyContinue
            }

        Start-Sleep -Seconds 1

        if (Test-Path $chromeUserDataDir) {
            Remove-Item $chromeUserDataDir -Recurse -Force -ErrorAction SilentlyContinue
        }
    } catch {}
}

# =========================================================
# FENÊTRE
# =========================================================
$form = New-Object System.Windows.Forms.Form
$form.Text = "MiniFacturier"
$form.Width = 340
$form.Height = 325
$form.StartPosition = "CenterScreen"
$form.FormBorderStyle = "FixedDialog"
$form.MaximizeBox = $false

$laragonLabel = New-Object System.Windows.Forms.Label
$laragonLabel.Text = "OPEN LARAGON"
$laragonLabel.Width = 280
$laragonLabel.Height = 35
$laragonLabel.Left = 30
$laragonLabel.Top = 15
$laragonLabel.TextAlign = "MiddleCenter"
$laragonLabel.ForeColor = [System.Drawing.Color]::White
$laragonLabel.BackColor = [System.Drawing.Color]::Crimson
$laragonLabel.Font = New-Object System.Drawing.Font("Segoe UI", 10, [System.Drawing.FontStyle]::Bold)

$buttonOn = New-Object System.Windows.Forms.Button
$buttonOn.Text = "ON"
$buttonOn.Width = 220
$buttonOn.Height = 40
$buttonOn.Left = 55
$buttonOn.Top = 65
$buttonOn.Enabled = $false
$buttonOn.Font = New-Object System.Drawing.Font("Segoe UI", 10, [System.Drawing.FontStyle]::Bold)

$buttonOff = New-Object System.Windows.Forms.Button
$buttonOff.Text = "OFF"
$buttonOff.Width = 220
$buttonOff.Height = 40
$buttonOff.Left = 55
$buttonOff.Top = 120
$buttonOff.Font = New-Object System.Drawing.Font("Segoe UI", 10, [System.Drawing.FontStyle]::Bold)

$statusLabel = New-Object System.Windows.Forms.Label
$statusLabel.Text = "Status: CLOSED"
$statusLabel.Width = 280
$statusLabel.Height = 30
$statusLabel.Left = 30
$statusLabel.Top = 170
$statusLabel.TextAlign = "MiddleCenter"
$statusLabel.ForeColor = [System.Drawing.Color]::Red
$statusLabel.Font = New-Object System.Drawing.Font("Segoe UI", 10, [System.Drawing.FontStyle]::Bold)

$progressBar = New-Object System.Windows.Forms.ProgressBar
$progressBar.Width = 280
$progressBar.Height = 22
$progressBar.Left = 30
$progressBar.Top = 215
$progressBar.Minimum = 0
$progressBar.Maximum = 100
$progressBar.Value = 0

$progressLabel = New-Object System.Windows.Forms.Label
$progressLabel.Text = "0% - Waiting"
$progressLabel.Width = 280
$progressLabel.Height = 25
$progressLabel.Left = 30
$progressLabel.Top = 242
$progressLabel.TextAlign = "MiddleCenter"
$progressLabel.Font = New-Object System.Drawing.Font("Segoe UI", 9, [System.Drawing.FontStyle]::Bold)

# =========================================================
# BOUTON ON
# =========================================================
$buttonOn.Add_Click({

    $script:isLaunching = $true
    $buttonOn.Enabled = $false
    $buttonOff.Enabled = $false

    if (-not (Test-LaragonReady)) {
        [System.Windows.Forms.MessageBox]::Show(
            "Laragon is not running. Please open Laragon before launching the application.",
            "Laragon required",
            [System.Windows.Forms.MessageBoxButtons]::OK,
            [System.Windows.Forms.MessageBoxIcon]::Warning
        )

        $statusLabel.Text = "Status: LARAGON NOT READY"
        $statusLabel.ForeColor = [System.Drawing.Color]::Red
        Set-Progress 0 "Laragon not ready"

        $script:isLaunching = $false
        Update-LaragonStatus
        $buttonOff.Enabled = $true
        return
    }

    Start-DockerDesktopSilent

    if (-not (Wait-Docker 90)) {
        [System.Windows.Forms.MessageBox]::Show(
            "Docker Desktop is not ready. Please start Docker Desktop manually and try again.",
            "Docker required",
            [System.Windows.Forms.MessageBoxButtons]::OK,
            [System.Windows.Forms.MessageBoxIcon]::Error
        )

        $statusLabel.Text = "Status: DOCKER NOT READY"
        $statusLabel.ForeColor = [System.Drawing.Color]::Red
        Set-Progress 0 "Docker not ready"

        $script:isLaunching = $false
        Update-LaragonStatus
        $buttonOff.Enabled = $true
        return
    }

    Start-GotenbergSafe

    if (-not (Wait-Gotenberg 40)) {
        [System.Windows.Forms.MessageBox]::Show(
            "Gotenberg did not start correctly. Test manually: curl.exe http://127.0.0.1:3000/health",
            "Gotenberg error",
            [System.Windows.Forms.MessageBoxButtons]::OK,
            [System.Windows.Forms.MessageBoxIcon]::Error
        )

        $statusLabel.Text = "Status: GOTENBERG ERROR"
        $statusLabel.ForeColor = [System.Drawing.Color]::Red
        Set-Progress 0 "Gotenberg error"

        $script:isLaunching = $false
        Update-LaragonStatus
        $buttonOff.Enabled = $true
        return
    }

    Start-MailpitSafe

    if (-not (Wait-Mailpit 40)) {
        [System.Windows.Forms.MessageBox]::Show(
            "Mailpit did not start correctly on port 8025.",
            "Mailpit error",
            [System.Windows.Forms.MessageBoxButtons]::OK,
            [System.Windows.Forms.MessageBoxIcon]::Error
        )

        $statusLabel.Text = "Status: MAILPIT ERROR"
        $statusLabel.ForeColor = [System.Drawing.Color]::Red
        Set-Progress 0 "Mailpit error"

        $script:isLaunching = $false
        Update-LaragonStatus
        $buttonOff.Enabled = $true
        return
    }

    if (-not (Start-SymfonySafe)) {
        [System.Windows.Forms.MessageBox]::Show(
            "Symfony server did not start correctly on port 8000.",
            "Symfony error",
            [System.Windows.Forms.MessageBoxButtons]::OK,
            [System.Windows.Forms.MessageBoxIcon]::Error
        )

        $statusLabel.Text = "Status: SYMFONY ERROR"
        $statusLabel.ForeColor = [System.Drawing.Color]::Red
        Set-Progress 0 "Symfony error"

        $script:isLaunching = $false
        Update-LaragonStatus
        $buttonOff.Enabled = $true
        return
    }

    Set-Progress 98 "Opening application"

    Open-AppBrowser

    $statusLabel.Text = "Status: APP LAUNCHED"
    $statusLabel.ForeColor = [System.Drawing.Color]::Green
    Set-Progress 100 "Application launched"

    $script:isLaunching = $false
    Update-LaragonStatus
    $buttonOff.Enabled = $true
})

# =========================================================
# BOUTON OFF
# =========================================================
$buttonOff.Add_Click({

    $script:isLaunching = $true
    $buttonOn.Enabled = $false
    $buttonOff.Enabled = $false

    $statusLabel.Text = "Status: CLOSING..."
    $statusLabel.ForeColor = [System.Drawing.Color]::Orange

    Set-Progress 80 "Closing browser"
    Close-AppBrowser

    Set-Progress 65 "Stopping Symfony"
    Stop-SymfonySafe

    Set-Progress 45 "Stopping Gotenberg"
    docker stop gotenberg > $null 2>&1

    Set-Progress 25 "Stopping Mailpit"
    docker stop mailpit > $null 2>&1

    Start-Sleep -Seconds 2

    $statusLabel.Text = "Status: APP CLOSED"
    $statusLabel.ForeColor = [System.Drawing.Color]::Red
    Set-Progress 0 "Application closed"

    $laragonTimer.Stop()
    $form.Close()
})

# =========================================================
# AJOUT DES CONTRÔLES
# =========================================================
$form.Controls.Add($laragonLabel)
$form.Controls.Add($buttonOn)
$form.Controls.Add($buttonOff)
$form.Controls.Add($statusLabel)
$form.Controls.Add($progressBar)
$form.Controls.Add($progressLabel)

# =========================================================
# TIMER LARAGON
# =========================================================
$laragonTimer = New-Object System.Windows.Forms.Timer
$laragonTimer.Interval = 1000
$laragonTimer.Add_Tick({
    Update-LaragonStatus
})
$laragonTimer.Start()

$form.Add_Shown({
    Update-LaragonStatus
})

$form.Add_FormClosed({
    $laragonTimer.Stop()
})

$form.ShowDialog()