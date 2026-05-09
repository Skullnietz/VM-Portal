<div id="help-widget-container" class="help-widget-container">
    <div id="help-options" class="help-options">
        <a href="https://wa.me/527221126660" target="_blank" class="help-option whatsapp" title="WhatsApp">
            <i class="fab fa-whatsapp"></i>
            <span class="help-label">WhatsApp</span>
        </a>
        <a href="https://teams.microsoft.com/l/chat/0/0?users=carlos.guizar@urvina.com.mx" target="_blank"
            class="help-option teams" title="Microsoft Teams">
            <!-- Official Teams SVG Icon (sourced from Bootstrap Icons, colored) -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="24" height="24" class="teams-icon">
                <path
                    d="M9.186 4.797a2.42 2.42 0 1 0-2.86-2.448h1.178c.929 0 1.682.753 1.682 1.682zm-4.295 7.738h2.613c.929 0 1.682-.753 1.682-1.682V5.58h2.783a.7.7 0 0 1 .682.716v4.294a4.197 4.197 0 0 1-4.093 4.293c-1.618-.04-3-.99-3.667-2.35Zm10.737-9.372a1.674 1.674 0 1 1-3.349 0 1.674 1.674 0 0 1 3.349 0m-2.238 9.488-.12-.002a5.2 5.2 0 0 0 .381-2.07V6.306a1.7 1.7 0 0 0-.15-.725h1.792c.39 0 .707.317.707.707v3.765a2.6 2.6 0 0 1-2.598 2.598z"
                    fill="white" />
                <path
                    d="M.682 3.349h6.822c.377 0 .682.305.682.682v6.822a.68.68 0 0 1-.682.682H.682A.68.68 0 0 1 0 10.853V4.03c0-.377.305-.682.682-.682Zm5.206 2.596v-.72h-3.59v.72h1.357V9.66h.87V5.945z"
                    fill="white" />
            </svg>
            <span class="help-label">Teams</span>
        </a>
        <a href="mailto:carlos.guizar@urvina.com.mx" class="help-option email" title="Email">
            <i class="fas fa-envelope"></i>
            <span class="help-label">Email</span>
        </a>
    </div>
    <button id="help-fab" class="help-fab" onclick="toggleHelpWidget()">
        <i class="fas fa-headset"></i>
    </button>
</div>

<style>
    .help-widget-container {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 15px;
    }

    .help-fab {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background-color: #007bff;
        /* Primary color */
        color: white;
        border: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        transition: transform 0.3s ease, background-color 0.3s;
    }

    .help-fab:hover {
        background-color: #0056b3;
        transform: scale(1.1);
    }

    .help-options {
        display: flex;
        flex-direction: column;
        gap: 10px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(20px);
        transition: all 0.3s ease;
        align-items: flex-end;
    }

    .help-options.active {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .help-option {
        display: flex;
        align-items: center;
        justify-content: center;
        /* Center the icon */
        width: 50px;
        height: 50px;
        border-radius: 50%;
        color: white;
        text-decoration: none;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        transition: transform 0.2s;
        position: relative;
    }

    .help-option:hover {
        transform: scale(1.1);
        text-decoration: none;
        color: white;
    }

    .help-option i {
        font-size: 24px;
    }

    .help-option.whatsapp {
        background-color: #25D366;
    }

    .help-option.teams {
        background-color: #6264A7;
    }

    .help-option.email {
        background-color: #DB4437;
    }

    /* Check labels on hover */
    .help-label {
        position: absolute;
        right: 60px;
        background-color: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 14px;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.2s;
        white-space: nowrap;
        pointer-events: none;
    }

    .help-option:hover .help-label {
        opacity: 1;
        visibility: visible;
    }
</style>

<script>
    function toggleHelpWidget() {
        const options = document.getElementById('help-options');
        const fab = document.getElementById('help-fab');
        options.classList.toggle('active');

        // Optional: Rotate icon
        const icon = fab.querySelector('i');
        if (options.classList.contains('active')) {
            icon.classList.remove('fa-headset');
            icon.classList.add('fa-times');
        } else {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-headset');
        }
    }

    // Close when clicking outside
    document.addEventListener('click', function (event) {
        const container = document.getElementById('help-widget-container');
        const options = document.getElementById('help-options');
        if (!container.contains(event.target) && options.classList.contains('active')) {
            toggleHelpWidget();
        }
    });
</script>