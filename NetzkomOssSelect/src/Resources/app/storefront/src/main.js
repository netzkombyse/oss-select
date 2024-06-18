// import
import PluginManager from 'src/plugin-system/plugin.manager';
import NetzkomOssSelector from './plugin/netzkom-oss-selector/netzkom-oss-selector.plugin';

// register plugin
PluginManager.register(
    'NetzkomOssSelector',
    NetzkomOssSelector,
    'body'
);
