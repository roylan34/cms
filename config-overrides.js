const { override, fixBabelImports, addLessLoader } = require('customize-cra');

module.exports = override(
    fixBabelImports('import', {
        libraryName: 'antd',
        librayDirectory: 'es',
        style: true
    }),
    //Customize theme color
    addLessLoader({
        javascriptEnabled: true,
        modifyVars: { '@primary-color': '#1CC597' }
    })
)
