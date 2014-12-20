<?
namespace Sbnc;
require 'loader.php';

/**
 * sbnc
 *
 * Blocks Spam without any human interaction.
 *
 * @package    Sbnc
 * @author     Fabian Pirklbauer <hi@fabianweb.net>
 * @copyright  2014-2015 Fabian Pirklbauer
 * @license    https://github.com/fabianweb/sbnc/LICENSE.md
 * @version    0.2
 * @link       https://github.com/fabianweb/sbnc
 */

class Sbnc extends Core
{

    protected $modules = [
        'Time',
        'Hidden',
        'Gestures',
        'Content',
        'Validate',
        'Remote'
    ];

    protected $addons = [
        'Flasher'
    ];


    protected $utils = [
        'FlashMessages' // this util is required!
    ];

    /**
     * Options for sbnc.
     * The second entry for the prefix option should be changed. It
     * is the name for the prefix field, that holds the random prefix
     * for other fields (begin it with a letter!).
     *
     * @var array
     */
    protected $options = [
        'prefix' => ['random', 'a86jg5'],
        'javascript' => true,
        'html5' => true
    ];

}
