<?php

use Gaufrette\Adapter\Local as OriginalLocalAdapter;

class Brizy_Admin_Guafrette_LocalAdapter extends OriginalLocalAdapter {

	/**
	 * @param string $directory Directory where the filesystem is located
	 * @param bool $create Whether to create the directory if it does not
	 *                          exist (default FALSE)
	 * @param int $mode Mode for mkdir
	 *
	 * @throws RuntimeException if the specified directory does not exist and
	 *                          could not be created
	 */
	public function __construct( $directory, $create = false, $mode = 0777 ) {
		$this->directory = Brizy_Admin_Guafrette_Path::normalize( $directory );

		if ( is_link( $this->directory ) ) {
			$this->directory = realpath( $this->directory );
		}

		parent::__construct( $directory, $create, $mode );
	}

	protected function normalizePath( $path ) {
		$path = Brizy_Admin_Guafrette_Path::normalize( $path );

		if ( 0 !== strpos( $path, $this->directory ) ) {
			throw new \OutOfBoundsException( sprintf( 'The path "%s" is out of the filesystem.', $path ) );
		}

		return $path;
	}

	public function getUrl( $key ) {
		$urlBuilder = new Brizy_Editor_UrlBuilder( Brizy_Editor_Project::get(), null );

		return $urlBuilder->upload_url( $key );
	}

}
