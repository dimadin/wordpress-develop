<?php
/**
 * @group external-http
 */
class Tests_External_HTTP_OEmbed extends WP_UnitTestCase {
	/**
	 * Test secure youtube.com embeds
	 *
	 * @ticket 23149
	 */
	function test_youtube_com_secure_embed() {
		$out = wp_oembed_get( 'http://www.youtube.com/watch?v=oHg5SJYRHA0' );
		$this->assertContains( 'https://www.youtube.com/embed/oHg5SJYRHA0?feature=oembed', $out );

		$out = wp_oembed_get( 'https://www.youtube.com/watch?v=oHg5SJYRHA0' );
		$this->assertContains( 'https://www.youtube.com/embed/oHg5SJYRHA0?feature=oembed', $out );

		$out = wp_oembed_get( 'https://youtu.be/zHjMoNQN7s0' );
		$this->assertContains( 'https://www.youtube.com/embed/zHjMoNQN7s0?feature=oembed', $out );
	}

	/**
	 * Test m.youtube.com embeds
	 *
	 * @ticket 32714
	 */
	function test_youtube_com_mobile_embed() {
		$out = wp_oembed_get( 'http://m.youtube.com/watch?v=oHg5SJYRHA0' );
		$this->assertContains( 'https://www.youtube.com/embed/oHg5SJYRHA0?feature=oembed', $out );

		$out = wp_oembed_get( 'https://m.youtube.com/watch?v=oHg5SJYRHA0' );
		$this->assertContains( 'https://www.youtube.com/embed/oHg5SJYRHA0?feature=oembed', $out );
	}

	function test_youtube_embed_url() {
		global $wp_embed;
		$out = $wp_embed->autoembed( 'https://www.youtube.com/embed/QcIy9NiNbmo' );
		$this->assertContains( 'https://youtube.com/watch?v=QcIy9NiNbmo', $out );
	}

	function test_youtube_v_url() {
		global $wp_embed;
		$out = $wp_embed->autoembed( 'https://www.youtube.com/v/QcIy9NiNbmo' );
		$this->assertContains( 'https://youtube.com/watch?v=QcIy9NiNbmo', $out );
	}

	/**
   * Test Kindle Instant Previews embeds.
   *
   * @ticket 38181
   */
  function test_amazon_kindle_non_book_embed() {
					$out = wp_oembed_get( 'http://www.amazon.com/All-New-Kindle-E-reader-Glare-Free-Touchscreen/dp/B00ZV9PXP2/r' );
					$this->assertFalse( $out );

					$out = wp_oembed_get( 'https://www.amazon.com/All-New-Kindle-E-reader-Glare-Free-Touchscreen/dp/B00ZV9PXP2/r' );
					$this->assertFalse( $out );
	}

  /**
   * Support for Kindle previews shared from Kindle devices or apps.
   *
   * @link http://www.amazon.com/bettersharing Kindle Instant Previews sharing homepage
   *
   * @ticket 38181
   */
  function test_amazon_kindle_shared_preview_embed() {
					// Book.
					$out = wp_oembed_get( 'https://read.amazon.com/kp/kshare?asin=B008TW1HMG&tag=foo-20' );
					$this->assertContains( 'https://read.amazon.com/kp/card?', $out );
					$this->assertContains( 'asin=B008TW1HMG', $out );
					$this->assertContains( 'tag=foo-20', $out );

					// Quote.
					$out = wp_oembed_get( 'https://read.amazon.com/kp/kshare?asin=B00DPM7TIG&tag=foo-20&id=XD3ezAVVQ3KP0bWxSFvosg' );
					$this->assertContains( 'https://read.amazon.com/kp/card?', $out );
					$this->assertContains( 'asin=B00DPM7TIG', $out );
					$this->assertContains( 'tag=foo-20', $out );

					// Reading progress.
					$out = wp_oembed_get( 'https://read.amazon.com/kp/kshare?asin=B007P7HRS4&id=Sd6UmMYYTc6rHl1BYkXU2g&tag=foo-20' );
					$this->assertContains( 'https://read.amazon.com/kp/card?', $out );
					$this->assertContains( 'asin=B007P7HRS4', $out );
					$this->assertContains( 'tag=foo-20', $out );
  }

  /**
   * Support for Kindle previews created from the self-service embedding tool
   *
   * @link http://www.amazon.com/kindleinstantpreview Kindle Instant Previews for third parties
   *
   * @ticket 38181
   */
  function test_amazon_kindle_embedded_preview_embed() {
					// URL.
					$out = wp_oembed_get( 'https://read.amazon.com/kp/embed?asin=B008TW1HMG&tag=foo-20' );
					$this->assertContains( 'https://read.amazon.com/kp/card?', $out );
					$this->assertContains( 'asin=B008TW1HMG', $out );
					$this->assertContains( 'tag=foo-20', $out );

					// Interactive book card.
					$out = wp_oembed_get( 'https://read.amazon.com/kp/card?asin=B008TW1HMG&tag=foo-20&preview=inline' );
					$this->assertContains( 'https://read.amazon.com/kp/card?', $out );
					$this->assertContains( 'asin=B008TW1HMG', $out );
					$this->assertContains( 'tag=foo-20', $out );
  }

  /**
   * Support for Kindle Instant Previews in non-US marketplaces
   *
   * @ticket 38181
   */
  function test_amazon_kindle_preview_non_us_embed() {
					// Canada (canonical TLD: .ca)
					$out = wp_oembed_get( 'https://read.amazon.ca/kp/embed?asin=B008TW1HMG&tag=foo-20&ref_=bar_qux' );
					$this->assertContains( 'https://read.amazon.ca/kp/card?', $out );
					$this->assertContains( 'asin=B008TW1HMG', $out );
					$this->assertContains( 'tag=foo-20', $out );

					// India (canonical TLD: .in)
					$out = wp_oembed_get( 'https://read.amazon.in/kp/embed?asin=B008TW1HMG&tag=foo-20&ref_=bar_qux' );
					$this->assertContains( 'https://read.amazon.in/kp/card?', $out );
					$this->assertContains( 'asin=B008TW1HMG', $out );
					$this->assertContains( 'tag=foo-20', $out );

					// Asia (canonical TLD: .com.au)
					$out = wp_oembed_get( 'https://read.amazon.co.jp/kp/embed?asin=B008TW1HMG&tag=foo-20&ref_=bar_qux&preview=inline' );
					$this->assertContains( 'https://read.amazon.com.au/kp/card?', $out );
					$this->assertContains( 'asin=B008TW1HMG', $out );
					$this->assertContains( 'tag=foo-20', $out );

					// China (canonical TLD: .cn)
					$out = wp_oembed_get( 'https://read.amazon.cn/kp/embed?asin=B01M06TYLV&tag=foo-20&ref_=bar_qux&preview=inline' );
					$this->assertContains( 'https://read.amazon.cn/kp/card?', $out );
					$this->assertContains( 'asin=B01M06TYLV', $out );
					$this->assertContains( 'tag=foo-20', $out );
  }	
}
