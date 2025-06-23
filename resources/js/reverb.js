// resources/js/reverb.js
window.initializeReverb = function() {
    window.Echo.channel('action-channel')
        .listen('.ActionExecuted', (e) => {
            console.log('Event received:', e);
            
            const alertDiv = document.getElementById('alert');
            if (!alertDiv) return;
            
            alertDiv.innerHTML = `<div style="background:yellow;padding:10px;">
                <strong>ALERT!</strong> ${e.message}
            </div>`;
            setTimeout(() => alertDiv.innerHTML = '', 3000);
        })
        .error((error) => {
            console.error('Channel error:', error);
        });
};