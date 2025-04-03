const puppeteer = require('puppeteer');
const fs = require('fs');

(async () => {
    try {
        const args = process.argv.slice(2);
        if (args.length < 2) {
            console.error('Uso: node generate_pdf.js <ruta_html> <ruta_pdf>');
            process.exit(1);
        }

        const [htmlPath, pdfPath] = args;

        // Verificar si el archivo HTML existe
        if (!fs.existsSync(htmlPath)) {
            console.error('El archivo HTML no existe:', htmlPath);
            process.exit(1);
        }

        // Leer el contenido del archivo HTML
        const htmlContent = fs.readFileSync(htmlPath, 'utf8');

        // Lanzar navegador Headless Chrome
        const browser = await puppeteer.launch({ headless: 'new' });
        const page = await browser.newPage();

        // Cargar contenido HTML
        await page.setContent(htmlContent, { waitUntil: 'load' });

        // Generar el PDF
        await page.pdf({
            path: pdfPath,
            format: 'A4',
            printBackground: true
        });

        console.log('PDF generado:', pdfPath);

        await browser.close();
    } catch (error) {
        console.error('Error al generar PDF:', error);
        process.exit(1);
    }
})();
