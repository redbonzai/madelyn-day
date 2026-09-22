document.querySelectorAll('.qr-code[data-qr-url]').forEach((container, index) => {
    const destination = container.dataset.qrUrl;
    const bookTitle = container.dataset.bookTitle || 'this book';
    const qrTitle = container.dataset.qrTitle || `QR code to view ${bookTitle} on Amazon`;
    const qrDescription = container.dataset.qrDescription || `Scan with a phone camera to open the Amazon purchase page for ${bookTitle}.`;

    if (!destination || typeof qrcode !== 'function') {
        return;
    }

    const code = qrcode(0, 'M');
    code.addData(destination);
    code.make();
    container.innerHTML = code.createSvgTag({
        cellSize: 6,
        margin: 24,
        scalable: true,
        title: {
            id: `book-qr-title-${index}`,
            text: qrTitle,
        },
        alt: {
            id: `book-qr-description-${index}`,
            text: qrDescription,
        },
    });
});
