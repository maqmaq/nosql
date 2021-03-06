<?php
/**
 * phpillow CouchDB backend
 *
 * This file is part of phpillow.
 *
 * phpillow is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Lesser General Public License as published by the Free
 * Software Foundation; version 3 of the License.
 *
 * phpillow is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public License for
 * more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with phpillow; if not, write to the Free Software Foundation, Inc., 51
 * Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package Core
 * @version arbit-0.6-beta
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */

/**
 * Validate date inputs
 *
 * @package Core
 * @version arbit-0.6-beta
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */
class phpillowIndexableDateValidator extends phpillowValidator
{
    /**
     * Validate input as string
     * 
     * @param mixed $input 
     * @return string
     */
    public function validate( $input )
    {
        // Check if we received a unix timestamp, in this case we can just
        // directly convert and return it.
        if ( is_numeric( $input ) )
        {
            $date = new DateTime( '@' . $input );
        }
        // Otherwise we received most presumably some phpillowrary string, which
        // we first just try to parse with datetime (strtotime).
        else if ( ( $date = new DateTime( $input ) ) !== false )
        {
            // Already converted
        }
        // If IndexableDateTime could not parse the string, we got a problem. Maybe
        // handle more datetime formats here manually, but now we just fail.
        //
        // Since PHP 5.3 datetime seems to accept everything and just returns
        // NOW, if it fails to parse. So this seems untestable for now.
        else {
            throw new phpillowValidationException( 
                'Error parsing the date: %date', 
                array(
                    'date' => $input,
                )
            );
        }

        // Convert date to an array of ints, which is easily indexable inside
        // CouchDB views.
        return array(
            (int) $date->format( 'Y' ),
            (int) $date->format( 'm' ),
            (int) $date->format( 'd' ),
            (int) $date->format( 'H' ),
            (int) $date->format( 'i' ),
            (int) $date->format( 's' ),
        );
    }
}

