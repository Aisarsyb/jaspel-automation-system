/**
 * Jaspel Automation System (JAS) Frontend Javascript Helper.
 */
document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize Toasts Container
    if (!document.querySelector('.toast-container')) {
        const container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
});

// Toast Notification Manager
const Toast = {
    show(message, type = 'info', duration = 3000) {
        const container = document.querySelector('.toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;

        let icon = 'ℹ️';
        if (type === 'success') icon = '🟢';
        if (type === 'error') icon = '🔴';
        if (type === 'warning') icon = '⚠️';

        toast.innerHTML = `
            <div class="toast-icon">${icon}</div>
            <div class="toast-message">${message}</div>
        `;

        container.appendChild(toast);

        // Slide in
        setTimeout(() => toast.classList.add('show'), 50);

        // Slide out and remove
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    },
    success(message, duration = 3000) {
        this.show(message, 'success', duration);
    },
    error(message, duration = 4000) {
        this.show(message, 'error', duration);
    },
    warning(message, duration = 3500) {
        this.show(message, 'warning', duration);
    },
    info(message, duration = 3000) {
        this.show(message, 'info', duration);
    }
};

// Modal Manager
const Modal = {
    open(modalId) {
        const overlay = document.getElementById(modalId);
        if (overlay) {
            overlay.classList.add('open');
        }
    },
    close(modalId) {
        const overlay = document.getElementById(modalId);
        if (overlay) {
            overlay.classList.remove('open');
        }
    }
};

// AJAX Fetch Wrapper Helper
async function fetchAPI(url, options = {}) {
    const defaultHeaders = {
        'X-Requested-With': 'XMLHttpRequest'
    };

    if (options.body && !(options.body instanceof FormData) && typeof options.body === 'object') {
        options.headers = {
            ...defaultHeaders,
            'Content-Type': 'application/json',
            ...options.headers
        };
        options.body = JSON.stringify(options.body);
    } else {
        options.headers = {
            ...defaultHeaders,
            ...options.headers
        };
    }

    try {
        const response = await fetch(url, options);
        if (!response.ok) {
            const errText = await response.text();
            throw new Error(errText || `Server returned error status ${response.status}`);
        }
        return await response.json();
    } catch (error) {
        console.error('API Fetch error:', error);
        Toast.error(error.message || 'Gagal menghubungi server.');
        throw error;
    }
}
