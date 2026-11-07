import { register } from '@wordpress/data';
import {
	PluginDocumentSettingPanel,
	PluginSidebar,
	PluginSidebarMoreMenuItem,
} from '@wordpress/editor';
import { __ } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';
import './block-editor.css';
import icon from './components/concierge-icon';
import ConciergePanel from './components/concierge-panel';
import ConciergeSidebar from './components/concierge-sidebar';
import conciergeStore from './store/concerige';

/**
 * Register the main plugin.
 */
registerPlugin( 'bm-concierge', {
	render: () => (
		<>
			<PluginDocumentSettingPanel
				initialOpen={ true }
				title={ __( 'BM Concierge', 'bm-concierge' ) }
			>
				<ConciergePanel />
			</PluginDocumentSettingPanel>
			<PluginSidebarMoreMenuItem target="sidebar" icon={ icon }>
				{ __( 'BM Concierge', 'bm-concierge' ) }
			</PluginSidebarMoreMenuItem>
			<PluginSidebar
				name="sidebar"
				title={ __( 'BM Concierge', 'bm-concierge' ) }
				icon={ icon }
			>
				<ConciergeSidebar />
			</PluginSidebar>
		</>
	),
	icon: null,
} );

/**
 * Stores
 */
register( conciergeStore );
