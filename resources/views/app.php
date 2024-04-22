<?php
/**
* @var string $error
 * @var array $translations
 * @var array $churches
 * @var string $links
 * @var array $church_fields
 * @var array $limit
 * @var array $church_count_fields
*/
use function DT\Autolink\groups_label;
use function DT\Autolink\route_url;
$this->layout( 'layouts/tool' );
use DT\Autolink\Repositories\UserRepository;
use function DT\Autolink\container;
use function DT\Autolink\leader_share_url;

$user_repository = container()->make( UserRepository::class );
$user_name = $user_repository->display_name();
$coach_name = $user_repository->coach_name();
?>

<div class="container">
	<?php if ( !empty( $error ) ): ?>
		<dt-alert context="alert"
		          dismissable>
			<?php echo esc_html( $error ); ?>
		</dt-alert>
	<?php endif; ?>

	<dt-tile class="churches">
		<div class="section__inner">
			<div class="churches__heading">
				<h3><?php echo esc_attr( __( 'My', 'disciple-tools-autolink' ) . ' ' .  groups_label() ); ?></h3>
				<dt-button class="churches__add"
				           context="success"
				           href="<?php echo esc_url( route_url( '/groups/create' ) ) ?>"
				           rounded>
					<dt-icon icon="ic:baseline-plus"></dt-icon>
				</dt-button>
			</div>

			<div class="churches__list">

				<al-churches
                    id="leader-share-id"
                    value ='test'
                    share-url='<?php echo esc_url( leader_share_url() ); ?>'
					posts='<?php echo esc_attr( wp_json_encode( $churches['posts'] ) ); ?>'
					total="<?php echo esc_attr( $churches['total'] ) ?>"
					fields='<?php echo esc_attr( wp_json_encode( $church_fields ) ); ?>'
					limit='<?php echo esc_attr( wp_json_encode( $limit ) ); ?>'
					countFields='<?php echo esc_attr( wp_json_encode( $church_count_fields ) ); ?>'
				></al-churches>
			</div>
		</div>
	</dt-tile>
</div>
