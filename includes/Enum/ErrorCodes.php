<?php

namespace ACPL\AIAltGenerator\Enum;

use WP_Error;

enum ErrorCodes: string {
	case Not_image     = 'not_an_image';
	case No_API_key    = 'api_key_not_configured';
	case Img_not_found = 'image_not_found';

	public function get_label(): string {
		return match ( $this ) {
			self::Not_image => __( 'Attachment ID is not an image.', 'acpl-ai-alt-generator' ),
			self::No_API_key => __( "OpenAI's API key is not configured.", 'acpl-ai-alt-generator' ),
			self::Img_not_found => __( 'Image not found.', 'acpl-ai-alt-generator' ),
		};
	}

	public function to_wp_error( mixed $data = null ): WP_Error {
		return new WP_Error( $this->value, $this->get_label(), $data );
	}
}
