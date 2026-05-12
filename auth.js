// Sistema de autenticación con base de datos
document.addEventListener('DOMContentLoaded', function() {
    console.log('Auth.js cargado correctamente');
    
    if (document.getElementById('registerForm')) {
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            registerUser();
        });
    }
    
    if (document.getElementById('loginForm')) {
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            loginUser();
        });
    }
    
    if (document.getElementById('logoutLink')) {
        document.getElementById('logoutLink').addEventListener('click', function(e) {
            e.preventDefault();
            logoutUser();
        });
    }
    
    checkAuth();
    
    if (document.getElementById('userName')) {
        loadUserProfile();
    }
});

function registerUser() {
    const name = document.getElementById('registerName').value;
    const email = document.getElementById('registerEmail').value;
    const password = document.getElementById('registerPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    if (password !== confirmPassword) {
        alert('❌ Las contraseñas no coinciden');
        return false;
    }
    
    if (password.length < 6) {
        alert('❌ La contraseña debe tener al menos 6 caracteres');
        return false;
    }
    
    fetch('register.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ nombre: name, email: email, password: password })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            localStorage.setItem('celestial_currentUser', JSON.stringify(data.user));
            alert('✅ ¡Cuenta creada con éxito!');
            window.location.href = 'perfil.html';
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        alert('❌ Error al conectar con el servidor');
    });
}

function loginUser() {
    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;
    
    fetch('login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: email, password: password })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            localStorage.setItem('celestial_currentUser', JSON.stringify(data.user));
            alert('✅ ¡Bienvenido ' + data.user.name + '!');
            window.location.href = 'perfil.html';
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        alert('❌ Error al conectar con el servidor');
    });
}

function logoutUser() {
    fetch('logout.php')
    .then(response => response.json())
    .then(data => {
        localStorage.removeItem('celestial_currentUser');
        alert('👋 Sesión cerrada correctamente');
        window.location.href = 'inicio.html';
    })
    .catch(error => {
        localStorage.removeItem('celestial_currentUser');
        alert('👋 Sesión cerrada');
        window.location.href = 'inicio.html';
    });
}

function checkAuth() {
    const currentUser = JSON.parse(localStorage.getItem('celestial_currentUser'));
    const currentPage = window.location.pathname.split('/').pop();
    const protectedPages = ['perfil.html', 'mis-pedidos.html'];
    
    if (protectedPages.includes(currentPage) && !currentUser) {
        alert('🔒 Debes iniciar sesión para acceder a esta página');
        window.location.href = 'login.html';
        return false;
    }
    
    updateNavigation(currentUser);
    return true;
}

function updateNavigation(user) {
    const nav = document.querySelector('nav');
    if (!nav) return;
    
    // Eliminar SOLO los enlaces de autenticación, NO los botones de idioma/modo
    const authLinks = nav.querySelectorAll('a[href="login.html"], a[href="registro.html"], a[href="perfil.html"], #logoutLink');
    authLinks.forEach(link => link.remove());
    
    // Guardar los botones especiales
    const translateBtn = document.getElementById('translateBtn');
    const modoBtn = document.getElementById('modoBtn');
    
    if (user) {
        const profileLink = document.createElement('a');
        profileLink.href = 'perfil.html';
        profileLink.textContent = 'Mi Perfil';
        nav.insertBefore(profileLink, translateBtn || null);
        
        const logoutLink = document.createElement('a');
        logoutLink.href = '#';
        logoutLink.id = 'logoutLink';
        logoutLink.textContent = 'Cerrar Sesión';
        logoutLink.addEventListener('click', function(e) {
            e.preventDefault();
            logoutUser();
        });
        nav.insertBefore(logoutLink, translateBtn || null);
    } else {
        const loginLink = document.createElement('a');
        loginLink.href = 'login.html';
        loginLink.textContent = 'Iniciar Sesión';
        nav.insertBefore(loginLink, translateBtn || null);
        
        const registerLink = document.createElement('a');
        registerLink.href = 'registro.html';
        registerLink.textContent = 'Registrarse';
        nav.insertBefore(registerLink, translateBtn || null);
    }
}

function loadUserProfile() {
    const currentUser = JSON.parse(localStorage.getItem('celestial_currentUser'));
    
    if (currentUser) {
        if (document.getElementById('userName')) {
            document.getElementById('userName').textContent = currentUser.name;
        }
        if (document.getElementById('userEmail')) {
            document.getElementById('userEmail').textContent = currentUser.email;
        }
        if (document.getElementById('joinDate')) {
            document.getElementById('joinDate').textContent = currentUser.joinDate;
        }
        if (document.getElementById('orderDate')) {
            document.getElementById('orderDate').textContent = currentUser.joinDate;
        }
    }
}
