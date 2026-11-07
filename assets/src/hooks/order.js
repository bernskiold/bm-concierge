import { select, useDispatch } from '@wordpress/data';
import { useCallback, useState } from '@wordpress/element';

export const useOrder = () => {
	const { placeOrder } = useDispatch( 'bm/concierge' );
	const [ isOpen, setIsOpen ] = useState( false );
	const [ isSubmitted, setIsSubmitted ] = useState( false );

	const placeOrderFromData = useCallback(
		( data ) => {
			placeOrder( {
				...data,
				postId: select( 'core/editor' ).getCurrentPostId(),
			} );
			setIsSubmitted( true );
		},
		[ placeOrder ]
	);

	const openModal = () => setIsOpen( true );
	const closeModal = () => {
		setIsOpen( false );
		setIsSubmitted( false );
	};

	return {
		orderModalOpen: isOpen,
		orderSubmitted: isSubmitted,
		openOrderModal: openModal,
		closeOrderModal: closeModal,
		placeOrder: placeOrderFromData,
	};
};
