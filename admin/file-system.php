<?php

use Gaufrette\Adapter;
use Gaufrette\Filesystem;

/**
 * Class Brizy_FileSystem
 */
class Brizy_Admin_FileSystem {

	static private $fileSystem;
	static private $hasCache;

	/**
	 * @return Brizy_Admin_FileSystem
	 * @throws Exception
	 */
	public static function instance() {

		$adapter = apply_filters( 'brizy_filesystem_adapter', null );

		if ( ! $adapter ) {
			throw new Exception( 'Invalid file system adapter provided' );
		}

		return new self( $adapter );
	}

	/**
	 * @return Brizy_Admin_FileSystem
	 * @throws Exception
	 */
	public static function localInstance() {

		$urlBuilder = new Brizy_Editor_UrlBuilder( Brizy_Editor_Project::get() );
		$adapter    = new Brizy_Admin_Guafrette_LocalAdapter( $urlBuilder->upload_path(), true, 0755 );

		return new self( $adapter );
	}

	/**
	 * Brizy_Admin_FileSystem constructor.
	 *
	 * @param Brizy_Editor_Project $project
	 * @param Adapter $adapter
	 */
	private function __construct( Adapter $adapter ) {
		self::$fileSystem = new Filesystem( $adapter );
	}


	/**
	 * Will take a local file content a create a new key with the content
	 * This method will do nothing for the case when the keys are equal and the adapter is a local
	 *
	 *
	 * @param $key
	 * @param $content
	 *
	 * @return int
	 */
	public function loadFileInKey( $key, $localFile ) {
		return self::$fileSystem->write( $key, file_get_contents( $localFile ), true );
	}

	/**
	 * @param $key
	 * @param null $localFile
	 */
	public function writeFileLocally( $key, $localFile ) {
		$dirPath = dirname( $localFile );

		if ( ! file_exists( $dirPath ) ) {
			if ( ! mkdir( $dirPath, 0755, true ) && ! is_dir( $dirPath ) ) {
				throw new \RuntimeException( sprintf( 'Directory "%s" was not created', $dirPath ) );
			}
		}

		file_put_contents( $localFile, self::$fileSystem->read( $key ) );
	}

	/**
	 * @param $key
	 * @param $content
	 *
	 * @return int
	 */
	public function write( $key, $content ) {
		unset( self::$hasCache[ $key ] );

		return self::$fileSystem->write( $key, $content, true );
	}

	/**
	 * @param $key
	 *
	 * @return string
	 */
	public function read( $key ) {
		return self::$fileSystem->read( $key );
	}

	/**
	 * @param $key
	 *
	 * @return bool
	 */
	public function delete( $key ) {
		unset( self::$hasCache[ $key ] );

		return self::$fileSystem->delete( $key );
	}

	/**
	 * @param $key
	 * @param bool $ignoreCache
	 *
	 * @return bool
	 */
	public function has( $key, $ignoreCache = false ) {

		if ( isset( self::$hasCache[ $key ] ) && !$ignoreCache ) {
			return self::$hasCache[ $key ];
		}

		return self::$hasCache[ $key ] = self::$fileSystem->has( $key );
	}

	/**
	 * @param $key
	 */
	public function getUrl( $key ) {
		if ( $this->has( $key ) ) {
			return self::$fileSystem->getAdapter()->getUrl( $key );
		}

		return null;
	}

	/**
	 * @param $pageUploadPath
	 *
	 * @return bool
	 */
	public function deleteAllDirectories( $pageUploadPath ) {
		try {
			$dIterator = new DirectoryIterator( $pageUploadPath );
			foreach ( $dIterator as $entry ) {
				if ( ! $entry->isDot() && $entry->isDir() ) {
					$subDirIterator = new RecursiveDirectoryIterator( $entry->getRealPath(), RecursiveDirectoryIterator::SKIP_DOTS );
					$files          = new RecursiveIteratorIterator( $subDirIterator, RecursiveIteratorIterator::CHILD_FIRST );
					foreach ( $files as $file ) {
						if ( ! $file->isDir() ) {
							@unlink( $file->getRealPath() );
						}
					}

					self::deleteFilesAndDirectory( $entry->getRealPath() );
				}
			}
		} catch ( Exception $e ) {
			return false;
		}
	}


	/**
	 * @param $pageUploadPath
	 *
	 * @return bool
	 */
	public function deleteFilesAndDirectory( $pageUploadPath ) {
		try {
			$dIterator = new DirectoryIterator( $pageUploadPath );
			foreach ( $dIterator as $entry ) {
				if ( $entry->isDot() ) {
					continue;
				}

				if ( $entry->isDir() ) {
					$subDirIterator = new RecursiveDirectoryIterator( $entry->getRealPath(), RecursiveDirectoryIterator::SKIP_DOTS );
					$files          = new RecursiveIteratorIterator( $subDirIterator, RecursiveIteratorIterator::CHILD_FIRST );
					foreach ( $files as $file ) {
						if ( ! $file->isDir() ) {
							@unlink( $file->getRealPath() );
						}
					}

					@rmdir( $entry->getRealPath() );
				} else {
					@unlink( $entry->getRealPath() );
				}
			}

			@rmdir( $pageUploadPath );
		} catch ( Exception $e ) {
			return false;
		}

	}
}
