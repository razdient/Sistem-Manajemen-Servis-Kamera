import Chart from 'chart.js/auto';

window.Chart = Chart;
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

window.sendWhatsappDocument = async function ({ pdfUrl, whatsappNumber, message, filename }) {
    if (!whatsappNumber) {
        alert('Nomor WhatsApp pelanggan belum tersedia atau tidak valid.');
        return;
    }

    const waUrl = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(message)}`;

    try {
        const response = await fetch(pdfUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        });

        if (!response.ok) {
            throw new Error('Gagal membuat PDF.');
        }

        const blob = await response.blob();
        const file = new File([blob], filename, { type: 'application/pdf' });

        if (navigator.canShare && navigator.canShare({ files: [file] })) {
            try {
                await navigator.share({
                    files: [file],
                    title: filename,
                    text: message,
                });
                return;
            } catch (error) {
                if (error && error.name === 'AbortError') {
                    return;
                }
                // Lanjut ke fallback jika share gagal.
            }
        }

        const objectUrl = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = objectUrl;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        link.remove();
        URL.revokeObjectURL(objectUrl);

        window.open(waUrl, '_blank');
        alert('PDF berhasil diunduh. WhatsApp telah dibuka dengan pesan siap kirim. Silakan lampirkan PDF yang sudah diunduh, lalu kirim pesan.');
    } catch (error) {
        window.open(waUrl, '_blank');
        alert('PDF tidak dapat dibuat otomatis. WhatsApp telah dibuka dengan pesan siap kirim. Silakan unduh/cetak PDF secara manual lalu lampirkan.');
    }
};