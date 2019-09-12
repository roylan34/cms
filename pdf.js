const puppeteer = require('puppeteer');
const express = require('express');

const app = express();
var envrs = process.env.NODE_ENVS;

function getEnv() {
    console.log(envrs, "production");
    if (envrs == "production") {
        console.log(111)
        return 'http://localhost/cms';
    } else {
        console.log(222)
        return 'http://localhost:4000';
    }
}

app.get('/downloadpdf', async (req, res) => {


    // Respond with the PDF Buffer
    try {
        //Enable cors
        res.header('Access-Control-Allow-Origin', '*');
        res.header('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');

        const browser = await puppeteer.launch();
        const page = await browser.newPage();
        let env = null;

        process.on('unhandledRejection', function (reason, promise) {
            env = getEnv();
        })
        await page.goto(`${env}/template/pma`, { waitUntil: "networkidle0" });
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