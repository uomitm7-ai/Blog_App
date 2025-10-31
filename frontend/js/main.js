function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

async function checkAuth() {
    try {
        const res = await fetch('/blog-app/backend/api/auth/check.php', {
            credentials: 'same-origin'
        });
        const data = await res.json();
        return data.user || null;
    } catch {
        return null;
    }
}

async function logout(e) {
    e.preventDefault();
    await fetch('/blog-app/backend/api/auth/logout.php', {
        credentials: 'same-origin'
    });
    window.location.href = 'login.html';
}