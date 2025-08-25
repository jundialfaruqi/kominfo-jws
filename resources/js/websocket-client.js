/**
 * WebSocket Client for Laravel Reverb
 * Menggantikan AJAX polling dengan real-time WebSocket connections
 */

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Configure Pusher for Laravel Echo
window.Pusher = Pusher;

// Initialize Laravel Echo with Reverb configuration
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

class WebSocketManager {
    constructor() {
        this.channels = {};
        this.isConnected = false;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        this.reconnectDelay = 1000;
        this.slug = this.getSlugFromUrl();
        
        this.initializeConnection();
        this.setupChannels();
    }

    getSlugFromUrl() {
        return window.location.pathname.replace(/^\//,'');
    }

    initializeConnection() {
        // Listen for connection events
        window.Echo.connector.pusher.connection.bind('connected', () => {
            console.log('WebSocket connected successfully');
            this.isConnected = true;
            this.reconnectAttempts = 0;
            
            // Trigger initial data fetch when connected
            this.requestInitialData();
        });

        window.Echo.connector.pusher.connection.bind('disconnected', () => {
            console.log('WebSocket disconnected');
            this.isConnected = false;
            this.handleReconnection();
        });

        window.Echo.connector.pusher.connection.bind('error', (error) => {
            console.error('WebSocket error:', error);
            this.handleReconnection();
        });
    }

    handleReconnection() {
        if (this.reconnectAttempts < this.maxReconnectAttempts) {
            this.reconnectAttempts++;
            console.log(`Attempting to reconnect... (${this.reconnectAttempts}/${this.maxReconnectAttempts})`);
            
            setTimeout(() => {
                window.Echo.connector.pusher.connect();
            }, this.reconnectDelay * this.reconnectAttempts);
        } else {
            console.error('Max reconnection attempts reached. Falling back to polling.');
            this.fallbackToPolling();
        }
    }

    setupChannels() {
        // Server time updates
        this.channels.serverUpdates = window.Echo.channel('server-updates')
            .listen('.server.time.updated', (data) => {
                this.handleServerTimeUpdate(data);
            });

        // Audio updates
        this.channels.audioUpdates = window.Echo.channel('audio-updates')
            .listen('.audio.updated', (data) => {
                this.handleAudioUpdate(data);
            });

        // Content updates (marquee, slides, jumbotron, petugas)
        this.channels.contentUpdates = window.Echo.channel('content-updates')
            .listen('.content.updated', (data) => {
                this.handleContentUpdate(data);
            });

        // Adzan updates
        this.channels.adzanUpdates = window.Echo.channel('adzan-updates')
            .listen('.adzan.updated', (data) => {
                this.handleAdzanUpdate(data);
            });

        // Profile updates
        this.channels.profileUpdates = window.Echo.channel('profile-updates')
            .listen('.profile.updated', (data) => {
                this.handleProfileUpdate(data);
            });
    }

    requestInitialData() {
        // Request initial data when WebSocket connects
        if (this.slug) {
            // Trigger server to broadcast current data
            fetch(`/broadcast/server-time`, { method: 'POST' });
            fetch(`/broadcast/audio/${this.slug}`, { method: 'POST' });
            fetch(`/broadcast/marquee/${this.slug}`, { method: 'POST' });
            fetch(`/broadcast/slides/${this.slug}`, { method: 'POST' });
            fetch(`/broadcast/jumbotron`, { method: 'POST' });
            fetch(`/broadcast/petugas/${this.slug}`, { method: 'POST' });
            fetch(`/broadcast/adzan/${this.slug}`, { method: 'POST' });
            fetch(`/broadcast/profile/${this.slug}`, { method: 'POST' });
            fetch(`/broadcast/theme-check/${this.slug}`, { method: 'POST' });
        }
    }

    handleServerTimeUpdate(data) {
        console.log('Server time updated:', data);
        
        // Update global variables if they exist
        if (typeof window.updateServerTime === 'function') {
            window.updateServerTime(data.server_time);
        }
        
        // Update DOM elements
        const timeElements = document.querySelectorAll('.server-time');
        timeElements.forEach(element => {
            element.textContent = data.server_time;
        });
    }

    handleAudioUpdate(data) {
        console.log('Audio updated:', data);
        
        // Only process if this is for the current slug
        if (data.audio && data.audio.slug === this.slug) {
            if (typeof window.updateAudio === 'function') {
                window.updateAudio(data.audio);
            }
        }
    }

    handleContentUpdate(data) {
        console.log('Content updated:', data);
        
        switch (data.type) {
            case 'marquee':
                if (data.data.slug === this.slug && typeof window.updateMarquee === 'function') {
                    window.updateMarquee(data.data);
                }
                break;
            case 'slides':
                if (data.data.slug === this.slug && typeof window.updateSlides === 'function') {
                    window.updateSlides(data.data);
                }
                break;
            case 'jumbotron':
                if (typeof window.updateJumbotron === 'function') {
                    window.updateJumbotron(data.data);
                }
                break;
            case 'petugas':
                if (data.data.slug === this.slug && typeof window.updatePetugas === 'function') {
                    window.updatePetugas(data.data);
                }
                break;
            case 'theme':
                if (data.data.slug === this.slug && typeof window.updateTheme === 'function') {
                    window.updateTheme(data.data);
                }
                break;
        }
    }

    handleAdzanUpdate(data) {
        console.log('Adzan updated:', data);
        
        // Only process if this is for the current slug
        if (data.adzan && data.adzan.slug === this.slug) {
            if (typeof window.updateAdzan === 'function') {
                window.updateAdzan(data.adzan);
            }
        }
    }

    handleProfileUpdate(data) {
        console.log('Profile updated:', data);
        
        // Only process if this is for the current slug
        if (data.profile && data.profile.slug === this.slug) {
            if (typeof window.updateProfile === 'function') {
                window.updateProfile(data.profile);
            }
        }
    }

    fallbackToPolling() {
        console.log('Falling back to traditional AJAX polling');
        
        // Re-enable the original polling functions if they exist
        if (typeof window.enablePollingFallback === 'function') {
            window.enablePollingFallback();
        }
    }

    disconnect() {
        // Disconnect all channels
        Object.keys(this.channels).forEach(channelName => {
            if (this.channels[channelName]) {
                window.Echo.leaveChannel(channelName);
            }
        });
        
        // Disconnect Echo
        if (window.Echo) {
            window.Echo.disconnect();
        }
    }
}

// Initialize WebSocket manager when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.webSocketManager = new WebSocketManager();
});

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    if (window.webSocketManager) {
        window.webSocketManager.disconnect();
    }
});

export default WebSocketManager;