const { override, fixBabelImports, addLessLoader, addPostcssPlugins } = require('customize-cra');

module.exports = override(
    fixBabelImports('import', {
        libraryName: 'antd',
        librayDirectory: 'es',
        style: true
    }),
    //Customize theme color
    addLessLoader({
        javascriptEnabled: true
        // modifyVars: { '@primary-color': '#1DA57A' }
    })
)
