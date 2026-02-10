/**
 * Real-Time Results Updates
 * 
 * This module handles real-time updates for the tabulator results page.
 * It listens for score submission events and updates the display accordingly.
 */

class RealtimeResults {
    constructor() {
        this.updateInProgress = false;
        this.connectionStatus = 'disconnected';
        this.retryCount = 0;
        this.maxRetries = 3;
        this.retryDelay = 5000; // 5 seconds
        
        this.init();
    }

    init() {
        // Add connection status indicator first
        this.addConnectionStatusIndicator();
        
        // Check if Echo is available (broadcasting is configured)
        if (typeof window.Echo !== 'undefined') {
            this.setupEchoListener();
        } else {
            // Fallback to polling if Echo is not available
            console.log('Broadcasting not configured. Using polling fallback.');
            this.updateConnectionStatus('polling');
            this.setupPolling();
        }
    }

    /**
     * Set up Laravel Echo listener for real-time updates
     */
    setupEchoListener() {
        try {
            window.Echo.channel('results-channel')
                .listen('.scores.submitted', (event) => {
                    console.log('Score submission received:', event);
                    this.handleScoreUpdate(event);
                })
                .error((error) => {
                    console.error('Echo error:', error);
                    this.updateConnectionStatus('error');
                    this.handleConnectionFailure();
                });

            this.updateConnectionStatus('connected');
        } catch (error) {
            console.error('Failed to setup Echo listener:', error);
            this.updateConnectionStatus('error');
            this.setupPolling();
        }
    }

    /**
     * Fallback polling mechanism when broadcasting is not available
     */
    setupPolling() {
        // Poll every 10 seconds
        setInterval(() => {
            if (!this.updateInProgress) {
                this.fetchAndUpdateResults();
            }
        }, 10000);
    }

    /**
     * Handle score update event
     */
    handleScoreUpdate(event) {
        // Show notification
        this.showNotification(
            `New scores submitted by ${event.judge_name} for ${event.category_name}`
        );

        // Fetch and update results
        this.fetchAndUpdateResults();
    }

    /**
     * Fetch updated results from server and update display
     */
    async fetchAndUpdateResults() {
        if (this.updateInProgress) {
            return;
        }

        this.updateInProgress = true;
        this.showLoadingIndicator();

        try {
            const response = await window.axios.get(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (response.data.html) {
                this.updateResultsDisplay(response.data.html);
            } else {
                // If full HTML not provided, reload the page
                window.location.reload();
            }

            this.retryCount = 0; // Reset retry count on success
        } catch (error) {
            console.error('Failed to fetch results:', error);
            this.handleFetchError(error);
        } finally {
            this.updateInProgress = false;
            this.hideLoadingIndicator();
        }
    }

    /**
     * Update only the affected portions of the display
     */
    updateResultsDisplay(html) {
        // Parse the HTML response
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        // Update progress section
        const progressSection = doc.querySelector('.judging-progress-section');
        if (progressSection) {
            const currentProgress = document.querySelector('.judging-progress-section');
            if (currentProgress) {
                currentProgress.innerHTML = progressSection.innerHTML;
            }
        }

        // Update results table
        const resultsTable = doc.querySelector('.results-table tbody');
        if (resultsTable) {
            const currentTable = document.querySelector('.results-table tbody');
            if (currentTable) {
                // Animate the update
                currentTable.style.opacity = '0.5';
                setTimeout(() => {
                    currentTable.innerHTML = resultsTable.innerHTML;
                    currentTable.style.opacity = '1';
                }, 200);
            }
        }
    }

    /**
     * Handle connection failure gracefully
     */
    handleConnectionFailure() {
        if (this.retryCount < this.maxRetries) {
            this.retryCount++;
            console.log(`Retrying connection (${this.retryCount}/${this.maxRetries})...`);
            
            setTimeout(() => {
                this.setupEchoListener();
            }, this.retryDelay);
        } else {
            console.log('Max retries reached. Falling back to polling.');
            this.setupPolling();
        }
    }

    /**
     * Handle fetch error
     */
    handleFetchError(error) {
        if (error.response && error.response.status === 401) {
            // User is not authenticated, redirect to login
            window.location.href = '/login';
        } else {
            this.showNotification('Failed to update results. Please refresh the page.', 'error');
        }
    }

    /**
     * Show notification to user
     */
    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transition-all duration-300 ${
            type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        } text-white`;
        notification.textContent = message;

        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 10);

        // Remove after 5 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 5000);
    }

    /**
     * Show loading indicator
     */
    showLoadingIndicator() {
        const indicator = document.getElementById('loading-indicator');
        if (indicator) {
            indicator.classList.remove('hidden');
        }
    }

    /**
     * Hide loading indicator
     */
    hideLoadingIndicator() {
        const indicator = document.getElementById('loading-indicator');
        if (indicator) {
            indicator.classList.add('hidden');
        }
    }

    /**
     * Add connection status indicator to the page
     */
    addConnectionStatusIndicator() {
        const indicator = document.createElement('div');
        indicator.id = 'connection-status';
        indicator.className = 'fixed bottom-4 right-4 px-4 py-2 rounded-full text-sm font-medium shadow-lg';
        indicator.innerHTML = '<span class="status-dot inline-block w-2 h-2 rounded-full mr-2"></span><span class="status-text">Connecting...</span>';
        
        document.body.appendChild(indicator);
        this.updateConnectionStatus('connecting');
    }

    /**
     * Update connection status indicator
     */
    updateConnectionStatus(status) {
        this.connectionStatus = status;
        const indicator = document.getElementById('connection-status');
        
        if (!indicator) return;

        const statusDot = indicator.querySelector('.status-dot');
        const statusText = indicator.querySelector('.status-text');

        switch (status) {
            case 'connected':
                indicator.className = 'fixed bottom-4 right-4 px-4 py-2 rounded-full text-sm font-medium shadow-lg bg-green-100 text-green-800';
                statusDot.className = 'status-dot inline-block w-2 h-2 rounded-full mr-2 bg-green-500 animate-pulse';
                statusText.textContent = 'Live';
                break;
            case 'polling':
                indicator.className = 'fixed bottom-4 right-4 px-4 py-2 rounded-full text-sm font-medium shadow-lg bg-blue-100 text-blue-800';
                statusDot.className = 'status-dot inline-block w-2 h-2 rounded-full mr-2 bg-blue-500 animate-pulse';
                statusText.textContent = 'Live (Polling)';
                break;
            case 'disconnected':
                indicator.className = 'fixed bottom-4 right-4 px-4 py-2 rounded-full text-sm font-medium shadow-lg bg-gray-100 text-gray-800';
                statusDot.className = 'status-dot inline-block w-2 h-2 rounded-full mr-2 bg-gray-500';
                statusText.textContent = 'Offline';
                break;
            case 'error':
                indicator.className = 'fixed bottom-4 right-4 px-4 py-2 rounded-full text-sm font-medium shadow-lg bg-red-100 text-red-800';
                statusDot.className = 'status-dot inline-block w-2 h-2 rounded-full mr-2 bg-red-500';
                statusText.textContent = 'Connection Error';
                break;
            case 'connecting':
                indicator.className = 'fixed bottom-4 right-4 px-4 py-2 rounded-full text-sm font-medium shadow-lg bg-yellow-100 text-yellow-800';
                statusDot.className = 'status-dot inline-block w-2 h-2 rounded-full mr-2 bg-yellow-500 animate-pulse';
                statusText.textContent = 'Connecting...';
                break;
        }
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        new RealtimeResults();
    });
} else {
    new RealtimeResults();
}

export default RealtimeResults;
