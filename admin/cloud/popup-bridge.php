<?php

/**
 * Class Brizy_Admin_Cloud_BlockUploader
 */
class Brizy_Admin_Cloud_PopupBridge extends Brizy_Admin_Cloud_AbstractBridge {


	/**
	 * @param Brizy_Editor_Block $layout
	 *
	 * @return mixed|void
	 * @throws Exception
	 */
	public function export( $layout ) {

		// check if the assets are uploaded in cloud
		// upload them if needed
		// create the block in cloud

		$media = json_decode( $layout->getMedia() );

		if ( ! $media || ! isset( $media->fonts ) ) {
			throw new Exception( 'No fonts property in media object' );
		}

		if ( ! $media || ! isset( $media->images ) ) {
			throw new Exception( 'No images property in media object' );
		}

		$bridge = new Brizy_Admin_Cloud_MediaBridge( $this->client );
		foreach ( $media->images as $uid ) {
			$bridge->export( $uid );
		}

		$bridge = new Brizy_Admin_Cloud_FontBridge( $this->client );
		foreach ( $media->fonts as $fontUid ) {
			$bridge->export( $fontUid );
		}

		$this->client->createOrUpdatePopup( $layout );
	}

	/**
	 * @param $popupId
	 *
	 * @return mixed|void
	 * @throws Exception
	 */
	public function import( $popupId ) {
		$popups = $this->client->getPopups( [ 'filter' => [ 'uid' => $popupId ] ] );

		if ( ! isset( $popups[0] ) ) {
			return;
		}

		$popup = $popups[0];

		$name = md5( time() );
		$post = wp_insert_post( array(
			'post_title'  => $name,
			'post_name'   => $name,
			'post_status' => 'publish',
			'post_type'   => Brizy_Admin_Popups_Main::CP_SAVED_POPUP
		) );

		if ( $post ) {
			$brizyPost = Brizy_Editor_Popup::get( $post, $popup['uid'] );
			$brizyPost->setMeta( $popup['meta'] );
			$brizyPost->setCloudId( $popup['id'] );
			$brizyPost->set_editor_data( $popup['data'] );
			$brizyPost->set_uses_editor( true );
			$brizyPost->set_needs_compile( true );
			$brizyPost->save();
		}
	}

	/**
	 * @param Brizy_Editor_Block $layout
	 *
	 * @return mixed|void
	 * @throws Exception
	 */
	public function delete( $layout ) {
		$this->client->deletePopup( $layout->getCloudId() );
	}
}