/** 
 * @param {*} value - Check if value is empty
 * @return {boolean} true | false
 */
export let isEmpty = value => value === null || value === '';

/**
 * @param {*} value - Check if value is empty.
 * @return {string} Return empty string.
 */
export let isEmptyStr = value => (value === null || value === '' ? '' : value);

/**
 * Generate random string.
 */
export let makeId = () => {
    return Math.random()
        .toString(36)
        .slice(-5);
};

/**
 * Split the string and translate array string value to array int value using map.
 * @param {string} arrStr  - comma delimited 
 */
export let splitStrToArrInt = (arrStr) => {
    if (arrStr !== null || arrStr !== '') {
        return arrStr.toString().split(',').map(function (i) {
            return parseInt(i, 10);
        });
    }
    return null;

}