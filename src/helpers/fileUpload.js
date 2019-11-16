import $ from 'jquery';
import { API_URL } from './constant';

export default class File {

    static upload({ url, attachment, data, cbSuccess, cbError, cbComplete }) {

        //Getting Files Collection
        let files = attachment;

        if (Object.keys(data).length > 0) {
            //Declaring new Form Data Instance  
            let formData = new FormData();

            //Looping through uploaded files collection in case there is a Multi File Upload. This also works for single i.e simply remove MULTIPLE attribute from file control in HTML.  
            for (let i = 0; i < files.length; i++) {
                if (files[i].hasOwnProperty('originFileObj')) {
                    formData.append("attachment-" + i, files[i].originFileObj);
                }
            }

            //Looping paramater data
            for (let key in data) {
                formData.append(key, data[key]);
            }

            $.ajax({
                type: 'POST',
                url: url,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function (data, xhr, status) {
                    cbSuccess && cbSuccess.call(null, data); //if callback exist execute it.
                },
                error: function (xhr, status) {
                    cbError && cbError.call(null, xhr, status);
                },
                complete: function () {
                    cbComplete && cbComplete();
                }

            });
            return true;
        }
    }

    static removeFile(dir_name, file_name, thisElem) {
        var filename = file_name || '';
        var dirname = dir_name || '';
        if (filename != '' && dirname != '') {
            $.ajax({
                url: API_URL + 'php/downloadAttachment.php',
                type: 'GET',
                data: { filename: filename, dirname: dirname, action: 'remove-file' },
                async: false,
                success: function (data) {
                    if (data[0] == 'success') {
                        $(thisElem).closest('div').fadeOut('slow', function () {
                            $(this).remove();
                        });
                    }
                },
                error: function () { alert('Something went wrong!'); }

            });

        } else {
            alert('Unable to remove file.');
        }
        // return false;
    }


}