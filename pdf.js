const puppeteer = require('puppeteer');
const express = require('express');

const app = express();
const envr = process.env.NODE_ENV;

//Get node environment.
function getEnv() {
    if (envr === "production") {
        return 'http://localhost/cms';
    }
    return 'http://localhost:3000';
}

app.get('/downloadpdf', async (req, res) => {


    // Respond with the PDF Buffer
    try {
        //Enable cors
        res.header('Access-Control-Allow-Origin', '*');
        res.header('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');

        const browser = await puppeteer.launch();
        const page = await browser.newPage();
        const env = getEnv();
        await page.goto(env + '/template/pma', { waitUntil: "networkidle0" });
        // await page.emulateMedia('screen'); //By default, Puppeteer generates a PDF using the print CSS media. If you want to print with screen CSS, call await page.emulateMedia('screen') before page.pdf().
        const pdf = await page.pdf({
            // path: './react.pdf', // path (relative to CWD) to save the PDF to.
            printBackground: true,// print background colors
            width: '21.59cm', // match the css width and height we set for our PDF
            height: '35.6cm',
        });

        await browser.close();
        res.send(pdf);
    }
    catch (err) {
        throw (err)
    }

})

app.listen(4000);