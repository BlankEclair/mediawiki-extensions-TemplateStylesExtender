<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 *
 * @file
 */

declare( strict_types=1 );

namespace MediaWiki\Extension\TemplateStylesExtender;

use InvalidArgumentException;
use MediaWiki\Extension\TemplateStylesExtender\Matcher\VarNameMatcher;
use Wikimedia\CSS\Grammar\Alternative;
use Wikimedia\CSS\Grammar\FunctionMatcher;
use Wikimedia\CSS\Grammar\Juxtaposition;
use Wikimedia\CSS\Grammar\KeywordMatcher;
use Wikimedia\CSS\Grammar\MatcherFactory;
use Wikimedia\CSS\Grammar\WhitespaceMatcher;
use Wikimedia\CSS\Sanitizer\StylePropertySanitizer;

class TemplateStylesExtender {

    /**
     * Adds a css var name matcher
     *
     * @param StylePropertySanitizer $propertySanitizer
     */
	public function addVarSelector( StylePropertySanitizer $propertySanitizer ): void {
		$propertySanitizer->setCssWideKeywordsMatcher(
			new FunctionMatcher(
				'var',
				new Juxtaposition( [
					new WhitespaceMatcher( [ 'significant' => false ] ),
					new VarNameMatcher(),
					new WhitespaceMatcher( [ 'significant' => false ] ),
				] )
			)
		);
	}

    /**
     * Adds the image-rendering matcher
     *
     * @param StylePropertySanitizer $propertySanitizer
     */
	public function addImageRendering( StylePropertySanitizer $propertySanitizer ): void {
		try {
			$propertySanitizer->addKnownProperties( [
				'image-rendering' => new KeywordMatcher( [
					'auto',
					'crisp-edges',
					'pixelated',
					'inherit',
					'initial',
					'unset',
				] )
			] );
		} catch ( InvalidArgumentException $e ) {
			// Fail silently
		}
	}

    /**
     * Adds the ruby-position and ruby-align matcher
     *
     * @param StylePropertySanitizer $propertySanitizer
     */
	public function addRuby( StylePropertySanitizer $propertySanitizer ): void {
		try {
			$propertySanitizer->addKnownProperties( [
				'ruby-position' => new KeywordMatcher( [
					'start',
					'center',
					'space-between',
					'space-around',
					'inherit',
					'initial',
					'unset',
				] )
			] );

			$propertySanitizer->addKnownProperties( [
				'ruby-align' => new KeywordMatcher( [
					'over',
					'under',
					'inter-character',
					'inherit',
					'initial',
					'unset',
				] )
			] );
		} catch ( InvalidArgumentException $e ) {
			// Fail silently
		}
	}

    /**
     * Adds scroll-margin-* and scroll-padding-* matcher
     * This is not well tested
     *
     * @param StylePropertySanitizer $propertySanitizer
     * @param MatcherFactory $factory
     */
	public function addScrollMarginProperties( StylePropertySanitizer $propertySanitizer, MatcherFactory $factory ): void {
		$suffixes = [
			'margin-block-end',
			'margin-block-start',
			'margin-block',
			'margin-bottom',
			'margin-inline-end',
			'margin-inline-start',
			'margin-inline',
			'margin-left',
			'margin-right',
			'margin-top',
			'margin',
			'padding-block-end',
			'padding-block-start',
			'padding-block',
			'padding-bottom',
			'padding-inline-end',
			'padding-inline-start',
			'padding-inline',
			'padding-left',
			'padding-right',
			'padding-top',
			'padding',
		];

		foreach ( $suffixes as $suffix ) {
			try {
				$propertySanitizer->addKnownProperties( [
					sprintf( 'scroll-%s', $suffix ) => new Alternative( [
						$factory->length()
					] )
				] );
			} catch ( InvalidArgumentException $e ) {
				// Fail silently
			}
		}
	}
}