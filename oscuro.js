// Modo oscuro/claro
document.addEventListener('DOMContentLoaded', function() {
    const nav = document.querySelector('nav');
    if (!nav) return;
    
    const modoBtn = document.createElement('button');
    modoBtn.id = 'modoBtn';
    modoBtn.style.background = '#7bd5ff';
    modoBtn.style.border = 'none';
    modoBtn.style.color = '#111';
    modoBtn.style.borderRadius = '3px';
    modoBtn.style.padding = '2px 8px';
    modoBtn.style.cursor = 'pointer';
    modoBtn.style.fontWeight = 'bold';
    modoBtn.style.marginLeft = '5px';
    modoBtn.onclick = toggleModo;
    
    nav.appendChild(modoBtn);
    
    const modoGuardado = localStorage.getItem('modo_oscuro');
    if (modoGuardado === 'true') {
        activarModoOscuro();
    } else {
        activarModoClaro();
    }
});

function toggleModo() {
    if (document.body.classList.contains('modo-oscuro')) {
        activarModoClaro();
    } else {
        activarModoOscuro();
    }
}

function activarModoOscuro() {
    document.body.classList.add('modo-oscuro');
    localStorage.setItem('modo_oscuro', 'true');
    document.getElementById('modoBtn').textContent = '🌙';
}

function activarModoClaro() {
    document.body.classList.remove('modo-oscuro');
    localStorage.setItem('modo_oscuro', 'false');
    document.getElementById('modoBtn').textContent = '☀️';
}
