<script>
const socket = new WebSocket('ws://localhost:6001/app/local');

socket.onopen = () => {
    console.log('✅ WebSocket connection established!');
    document.body.innerHTML += '<p>✅ WebSocket connected!</p>';
};

socket.onerror = (error) => {
    console.error('❌ WebSocket error:', error);
    document.body.innerHTML += `<p>❌ Connection failed: ${error.message}</p>`;
};
</script>