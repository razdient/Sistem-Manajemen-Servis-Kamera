
// Mengatur tema berdasarkan pilihan yang tersimpan
const savedTheme = localStorage.getItem('camera-service-theme');

if (savedTheme === 'dark') {
    document.documentElement.classList.add('dark');
} else {
    document.documentElement.classList.remove('dark');
}

// Fungsi untuk mengganti tema
window.toggleTheme = function () {
    const isDark = document.documentElement.classList.toggle('dark');

    localStorage.setItem(
        'camera-service-theme',
        isDark ? 'dark' : 'light'
    );
};
