<?php
/**
 * PDF Generator
 *
 * @package     TimeApp\PDFGenerator
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Generation class
 *
 * @since       1.0.0
 */
class TimeApp_Generate_PDF {


    public $pdf, $file, $id;


    /**
     * Setup the generator
     *
     * @access      public
     * @since       1.0.0
     * @param       string $file The path to the output file
     * @param       int $post_id The play we are building this for
     * @return      void
     */
    public function __construct( $file, $post_id ) {
        // We need the FPDF libs!
        require_once TIMEAPP_DIR . 'includes/libraries/fpdf/fpdf.php';
        require_once TIMEAPP_DIR . 'includes/class.timeapp-fpdf.php';

        $this->pdf  = new TimeApp_FPDF( 'P', 'mm', 'letter' );
        $this->file = $file;
        $this->id   = $post_id;
    }


    /**
     * Generate the file
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function build() {
        $point          = .35;
        $textualizer    = new TimeApp_Textualizer();
        $play           = get_post( $this->id );
        $effective_date = date( 'F jS, Y', strtotime( $play->post_date ) );
        $artist         = get_post_meta( $this->id, '_timeapp_artist', true );
        $artist         = get_post( $artist );
        $purchaser      = get_post_meta( $this->id, '_timeapp_purchaser', true );
        $purchaser      = get_post( $purchaser );
        $address        = get_post_meta( $purchaser->ID, '_timeapp_address', true );
        $city           = get_post_meta( $purchaser->ID, '_timeapp_city', true );
        $state          = get_post_meta( $purchaser->ID, '_timeapp_state', true );
        $zip_code       = get_post_meta( $purchaser->ID, '_timeapp_zip', true );
        $address        = $address . ', ' . $city . ', ' . $state . ' ' . $zip_code;
        $address        = trim( $address );
        $phone          = get_post_meta( $purchaser->ID, '_timeapp_phone_number', true );
        $email          = get_post_meta( $purchaser->ID, '_timeapp_email', true );
        $website        = get_post_meta( $purchaser->ID, '_timeapp_venue_url', true );
        $start_date     = get_post_meta( $this->id, '_timeapp_start_date', true );
        $end_date       = get_post_meta( $this->id, '_timeapp_end_date', true );
        $compensation   = get_post_meta( $this->id, '_timeapp_guarantee', true );
        $deposit1_date  = get_post_meta( $this->id, '_timeapp_deposit1_date', true );
        $deposit1_amt   = get_post_meta( $this->id, '_timeapp_deposit1_amt', true );
        $deposit2_date  = get_post_meta( $this->id, '_timeapp_deposit2_date', true );
        $deposit2_amt   = get_post_meta( $this->id, '_timeapp_deposit2_amt', true );
        $deposit3_date  = get_post_meta( $this->id, '_timeapp_deposit3_date', true );
        $deposit3_amt   = get_post_meta( $this->id, '_timeapp_deposit3_amt', true );
        $balance        = $compensation - (int) $deposit1_amt - (int) $deposit2_amt - (int) $deposit3_amt;
        $production_cost= get_post_meta( $this->id, '_timeapp_production_cost', true );
        $production     = get_post_meta( $this->id, '_timeapp_production', true ) ? 'Venue to provide production' : 'Artist to provide production';
        $notes          = get_post_meta( $this->id, '_timeapp_notes', true );
        $accommodations = get_post_meta( $this->id, '_timeapp_accommodations', true );
        $commission     = get_post_meta( $artist->ID, '_timeapp_commission', true );
        $contact_fname  = get_post_meta( $purchaser->ID, '_timeapp_first_name', true );
        $contact_lname  = get_post_meta( $purchaser->ID, '_timeapp_last_name', true );
        $contact_name   = '';

        // Is a contact first name specified?
        if( $contact_fname && $contact_fname != '' ) {
            $contact_name .= $contact_fname;
        }

        // Is a contact last name specified?
        if( $contact_lname && $contact_lname != '' ) {
            if( $contact_name != '' ) {
                $contact_name .= ' ';
            }

            $contact_name .= $contact_lname;
        }

        $this->pdf->SetMargins( 14, 14 );
        $this->pdf->SetAutoPageBreak( true, 18 );
        $this->pdf->AddPage();

        $this->pdf->SetFont( 'Arial', 'B', 22 );
        $this->pdf->Cell( 0, 22 * $point, 'TIME MUSIC AGENCY, INC', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'BU', 12 );
        $this->pdf->Cell( 0, 12 * $point, 'PO Box 353, Long Lake MN 55356   Office: (952) 448-4202   Fax: (952) 368-7281', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 14 );
        $this->pdf->Cell( 0, 14 * $point, 'www.timemusicagency.com', 0, 1, 'C' );
        
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 15 );
        $this->pdf->Cell( 0, 15 * $point, 'MUSICAL PERFORMANCE SERVICES AGREEMENT', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'BI', 11 );
        $this->pdf->MultiCell( 0, 11 * $point, 'THIS MUSICAL PERFORMANCE SERVICES AGREEMENT INCLUDES A "ARTIST TOUR RIDER" THAT IS ATTACHED AND INCORPORATED HEREIN BY REFERENCE', 0, 'C' );

        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->MultiCell( 0, 12 * $point, '    This Musical Performance Services Agreement (hereafter referred to as "Contract" or "Agreement") is entered into and agreed by the parties as bearing an effective date of ' . $effective_date . ' notwithstanding that either party may have laid hand and signed below on a date that varies from the effective date, by and between the person/s or entity/ies listed as "Purchaser" in paragraph 2 below (hereafter referred to as "Purchaser") with operating address, phone and e-mail of: ' . $address . ' / ' . $phone . ' / ' . $email . ' and ARTIST as represented by Time Music Agency, Inc, its sole authorized management company and agent, (hereafter referred to as "Artist" or "ARTIST" and, when applicable, "Time Music Agency, Inc,").' );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->MultiCell( 0, 12 * $point, '    Whereas Purchaser either as a natural person/s and/or business organization desires to hire live musical entertainment, and Artist desires to furnish the Purchaser its musical performance services (hereafter referred to as "engagement or performance"). Therefore in consideration of the promises of the parties and for other good and valuable consideration as set forth below, the Purchaser agrees to hire the Artist and the Artist agrees to perform for the Purchaser upon the terms and conditions as set forth in this agreement.' );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '1. Artist: ' );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->Cell( 0, 12 * $point, $artist->post_title, 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '2. Purchaser: ' );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->Cell( 0, 12 * $point, $purchaser->post_title, 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '3. Date(s) of Engagement:' );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->Cell( 0, 12 * $point, $start_date . ' - ' . $end_date, 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '4. Compensation Agreed Upon:' );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->MultiCell( 0, 12 * $point, timeapp_format_price( $compensation ) . ' (' . timeapp_format_price( $compensation, true ) . ')', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '5. Payment:', 0, 1 );

        $this->pdf->Cell( 15, 12 * $point, '5a.' );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->MultiCell( 0, 12 * $point, 'A non-refundable earnest money deposit in the amount of ' . timeapp_format_price( $deposit1_amt ) . ' made payable to Time Music Agency is due with signed contract;', 0, 1 );

        $last = 'b';

        if( $deposit2_amt && $deposit2_amt != '' ) {
            $this->pdf->SetFont( 'Times', 'B', 12 );
            $this->pdf->Cell( 15, 12 * $point, '5b.' );
            $this->pdf->SetFont( 'Times', '', 12 );
            $this->pdf->MultiCell( 0, 12 * $point, 'A non-refundable second deposit in the amount of ' . timeapp_format_price( $deposit2_amt ) . ' made payable to Time Music Agency is due by ' . date( 'F jS, Y', strtotime( $deposit2_date ) ) . ';', 0, 1 );

            $last = 'c';
        }

        if( $deposit3_amt && $deposit3_amt != '' ) {
            $this->pdf->SetFont( 'Times', 'B', 12 );
            $this->pdf->Cell( 15, 12 * $point, '5c.' );
            $this->pdf->SetFont( 'Times', '', 12 );
            $this->pdf->MultiCell( 0, 12 * $point, 'A non-refundable third deposit in the amount of ' . timeapp_format_price( $deposit3_amt ) . ' made payable to Time Music Agency is due by ' . date( 'F jS, Y', strtotime( $deposit3_date ) ) . ';', 0, 1 );

            $last = 'd';
        }

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 15, 12 * $point, '5' . $last . '.' );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->MultiCell( 0, 12 * $point, 'The remaining balance of ' . timeapp_format_price( $balance ) . ' is due, owing and shall be made payable to ARTIST prior to performance day of the show.', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->Cell( 0, 12 * $point, 'TIME IS OF THE ESSENCE ON ALL PAYMENTS DUE ARTIST.', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '6. Production:' );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->Cell( 0, 12 * $point, $production, 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '7. Additional Terms:' );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->MultiCell( 0, 12 * $point, ( $notes ? $notes : 'None' ), 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '8. Accommodations:' );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->MultiCell( 0, 12 * $point, ( $accommodations ? $accommodations : 'None' ), 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '9. PROOF OF ACCEPTANCE:', 0, 1 );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->MultiCell( 0, 12 * $point, 'Notwithstanding Purchaser\'s failure to return a signed copy of this Contract after delivery by fax, e-mail or U.S. mail of a copy or original document in paper or electronic form signed by Time Music Agency to Purchaser and should the parties herein, either directly or through their respective agents, employees or representatives engage in actions or make statements displaying acceptance of the terms herein, then this contract and all terms herein is deemed accepted and enforceable by the Artist and Purchaser.', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '10. TICKET PRICING AND VIP:', 0, 1 );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->MultiCell( 0, 12 * $point, 'In regards to all ticket prices in all categories to include General Admission, Seated, VIP, Comp, and Promo as well as any not directly mentioned here, ARTIST and purchaser must have mutual agreement in writing PRIOR to the on sale date of tickets. With regards to VIP areas and additional charges for VIP specifically, ARTIST does not approve without written permission any designated areas or charges for VIP. Purchaser must contact Time Music Agency, Inc. to discuss any and all VIP services in advance of on sale date.', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '11. FORCE MAJURE:', 0, 1 );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->MultiCell( 0, 12 * $point, 'The following is understood and agreed. Should ARTIST\'s performance be rendered impossible, or there comes to exist a significant frustration of purpose when an unforeseen event emerges, outside of ARTIST\'s direct control, that undermines ARTIST\'s principal purpose for performing under this contract or a hazardous condition exists or ARTIST is otherwise prevented or impaired due to Act(s) of God, sickness, accident, riots, strikes, labor dispute, interruption or failure of means of transportation or delivery of electrical power, earthquakes, epidemics, or any act or order of any government, including its administrative agencies, and/or any other same or similar cause or event, then ARTIST reserves the exclusive and sole right at its discretion to cancel its performance with no further obligation owing to Purchaser and Purchaser shall remain liable to pay ARTIST the full contract price plus any percentage moneys called for in the Contract regardless of the occurrence of any of the foregoing events.', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '12. INCLEMENT WEATHER:', 0, 1 );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->MultiCell( 0, 12 * $point, 'ARTIST\'s duty to perform per this contract shall be excused and ARTIST will have no liability to Purchaser if ARTIST determines, in its exclusive and sole right of discretion, that its performance is or will become impossible, hazardous, prevented or substantially impaired due to inclement weather. In such occurrence, Purchaser remains liable to ARTIST for the full contract price. Water in the air space or on the surface of the performance area can render that show cancellable at ARTIST\'s discretion.', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '13. PREMITS/LICENSES:', 0, 1 );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->MultiCell( 0, 12 * $point, 'Purchaser will secure and furnish all necessary permits and licenses necessary to schedule, implement and clear the ARTIST performance, including but not limited to, performing rights organizations specifically, ASCAP, BMI. SESAC, SoundExchange and any other similar or related organization clearing public performance rights; Federal, state, county, city and related governments including their agents, agencies and assigns; the landowner/s, or agents and assigns thereof, on whose land the ARTIST performance will take place.', 0, 1 );
 
        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '14. CREATIVE CONTROL:', 0, 1 );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->MultiCell( 0, 12 * $point, 'Purchaser shall have input over creative elements of Engagement including, without limitation, the creative elements of the following: sound, speakers, lights, set list.', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '15. RECORDING:', 0, 1 );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->MultiCell( 0, 12 * $point, 'Purchaser will not effect, advance, aid, or permit audio and/or visual broadcast or recording of all or any part of the ARTIST\'s show without the express written consent of the ARTIST.', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '16. MERCHANDISING:', 0, 1 );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->MultiCell( 0, 12 * $point, 'ARTIST retains one hundred percent (100%) of the gross receipts resulting from the sale of ARTIST or related Merchandise unless otherwise agreed to in writing 21 business days prior to the show. Purchaser shall provide well lit secure and prime locations for ARTIST merchandising and shall take action steps to prohibit the sale or distribution of all unauthorized merchandise on or adjacent to the Venue. Purchaser shall provide at least (1) one (8\') eight foot cafeteria/banquet style table to use to display Purchaser\'s merchandise.', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '17. PUBLICITY AND DIGITAL CONTENT:', 0, 1 );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->MultiCell( 0, 12 * $point, 'Purchaser enjoys a limited license to use the ARTIST\'s name and logo for promoting the engagement. All digital media released by the ARTIST to Purchaser remains the property of the ARTIST upon conclusion of the engagement. Purchaser will not modify or edit ARTIST media in whole or part without ARTIST\'s consent.', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '18. INSURANCE:', 0, 1 );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->MultiCell( 0, 12 * $point, 'Purchaser shall provide a one million dollar commercial General Liability insurance covering naming ARTIST and Time Music Agency, Inc. as additional insured and cover all claims, liabilities or losses directly or indirectly resulting from injuries to any person (including bodily and personal injury) and from any property damage and/or loss in connection with the ARTIST show. Said insurance shall be in full force and effect at all times during the day/s of ARTIST\'s show/s. Purchaser shall be the sole and exclusively obligated party to any non ARTIST controlled personnel, independent contractor/s or staff that works on the ARTIST performance contemplated herein and makes claim, demand or serves cause of action for workers compensation, personal injury, employment or independent contract law, labor law,  all government levels of taxation of any nature and type, tort claim, criminal conduct, immigration law or any claim in law or equity related directly or indirectly to this Contract and Purchaser shall hold harmless and release ARTIST and Time Music Agency, Inc herein.', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '19. FAILURE OF TERMS:', 0, 1 );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->MultiCell( 0, 12 * $point, 'Each of the terms and conditions of this Rider and Contract is necessary and essential for ARTIST\'s full performance of its obligations hereunder. Accordingly, if Purchaser refuses or neglects to fulfill all of the terms and conditions contained in the Rider or the Contract including, without limitation, the payment of any moneys due and any services and items required hereunder, then Purchaser shall be deemed in material breach of contract. In such event, ARTIST shall have the right, without waiver of any other rights and/or remedies, all of which are reserved to refuse to perform this Contract; to cancel the Engagement; and to retain any amounts paid to Purchaser as partial compensation.', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '20. PURCHASER\'S FINANCIAL INSTABILITY:', 0, 1 );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->MultiCell( 0, 12 * $point, 'By ARTIST\'s commercially reasonable assessment, if on or before the date of the Engagement, the financial condition or credit of Purchaser has been impaired or becomes unstable as evidenced, in part, by late or partial payments, or checks returned or ACH transfers rejected bearing "insufficient funds", ARTIST shall have the right to demand immediate payment of the full contract price specified herein. If purchaser fails or refuses to make such payment immediately, Purchaser shall be deemed in anticipatory breach of contract and ARTIST shall have the right, without further obligation to Purchaser to refuse to perform this contract; to cancel the Engagement; to retain any amounts paid to ARTIST as partial compensation; and Purchaser shall remain liable to ARTIST for the full contract price.', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '21. RE-BOOKING:', 0, 1 );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->MultiCell( 0, 12 * $point, 'In the event Purchaser, on its own effort and without involving Time Music Agency, Inc., books Artist into any establishment owned in whole or part, booked alone or with other parties, or controlled/represented by the Purchaser within twelve (12) months after the engagement date above, Time Music Agency, Inc shall be owed and paid by Purchaser a commission of ' . $commission . '% (' . $textualizer->textualize( $commission ) . ' percent) of the total contract price that Artist and Purchaser agreed to therein and Purchaser\'s failure to honor this clause is a material breach of this contract.', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '22. ACTS OF AGENTS BIND PURCHASER:', 0, 1 );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->MultiCell( 0, 12 * $point, 'A party communicating with ARTIST or Time Music Agency and acting in a manner of or holding him/her/itself out as an authorized agent of Purchaser has authority to bind Purchaser. ARTIST and Time Music Agency shall be held harmless and released from any disputes related to this Agreement that may arise between or among Purchaser and his/her/its agents, sub contractors, employees or representatives.', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 65, 12 * $point, '23. GENERAL GOVERNING TERMS:', 0, 1 );
        $this->pdf->SetFont( 'Times', '', 12 );
        $this->pdf->MultiCell( 0, 12 * $point, 'The parties represent to each other that each is free to enter into this Agreement, and that this engagement does not violate the terms of any agreement with any other third parties. This agreement is drafted and shall be construed under the laws of the State of Minnesota. No modification of this agreement shall occur unless in writing and signed by both parties. This agreement contains the entire understanding between the parties. Should any part of this agreement be judicially determined to be unenforceable, the remainder of it shall remain in force. Monetary amounts owed per this contract to ARTIST and Time Music Agency run in favor and inure to their respective heirs and assigns and payment obligations running against Purchaser shall survive any liquidation in bankruptcy. If Purchaser is a corporation, partnership or limited liability entity, then the natural persons owning an interest in Purchaser therein in whole or part shall be personally liable and personally guaranty Purchaser\'s obligations herein. If ARTIST and/or Time Music Agency are compelled to press rights under this agreement through litigation, arbitration or engagement of legal counsel, Purchaser shall pay ARTIST and/or Time Music Agency\'s legal fees in whole or part. Where appropriate in this Agreement, the masculine gender includes the feminine, the singular number includes the plural number and vice versa.', 0, 1 );

        $this->pdf->AddPage();

        $this->pdf->MultiCell( 0, 12 * $point, '    The parties have read, understood, and agree to the conditions and terms of this contract and any riders attached to this agreement. Both parties have had adequate time to review this contract with their respective legal counsel or advisors.', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->Cell( 10, 12 * $point, 'Dated:' );

        $x = $this->pdf->GetX();
        $y = $this->pdf->GetY() + 12 * $point;

        $this->pdf->Line( $x + 5, $y, $x + 55, $y );

        $this->pdf->SetX( 115 );

        $this->pdf->Cell( 10, 12 * $point, 'Dated:' );

        $x = $this->pdf->GetX();
        $y = $this->pdf->GetY() + 12 * $point;

        $this->pdf->Line( $x + 5, $y, $x + 55, $y );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );
        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->Cell( 0, 12 * $point, 'For Purchaser' );

        $this->pdf->SetX( 115 );

        $this->pdf->Cell( 0, 12 * $point, 'For ARTIST, LLC - Tax ID#: 27-1530759', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );
        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );
        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $x = $this->pdf->GetX();
        $y = $this->pdf->GetY() + 12 * $point;

        $this->pdf->Cell( 0, 12 * $point, 'X' );
        $this->pdf->Line( $x, $y, $x + 85, $y );

        $this->pdf->SetX( 115 );

        $x = $this->pdf->GetX();
        $y = $this->pdf->GetY() + 12 * $point;

        $this->pdf->Line( $x, $y, $x + 85, $y );

        $this->pdf->SetFont( 'Times', 'B', 12 );
        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->Cell( 0, 12 * $point, 'By:' );
        $this->pdf->SetX( 115 );
        $this->pdf->Cell( 0, 12 * $point, 'By:', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ( $contact_name != '' && $contact_name != $purchaser->post_title ? $contact_name . ', ' : '' ) . $purchaser->post_title );
        $this->pdf->SetX( 115 );
        $this->pdf->Cell( 0, 12 * $point, 'Mike Finding, CEO Time Music Agency', 0, 1 );

        $this->pdf->SetX( 115 );
        $this->pdf->Cell( 0, 12 * $point, 'a/o', 0, 1 );

        $this->pdf->SetX( 115 );
        $this->pdf->Cell( 0, 12 * $point, 'Chad Higgins, CFO Time Music Agency', 0, 1 );

        $this->pdf->Cell( 7, 12 * $point, 'Its' );
        
        $x = $this->pdf->GetX();
        $y = $this->pdf->GetY() + 12 * $point;

        $this->pdf->Line( $x, $y, $x + 78, $y );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->Cell( 0, 12 * $point, $purchaser->post_title );
        $this->pdf->SetX( 115 );
        $this->pdf->Cell( 0, 12 * $point, 'c/o Time Music Agency', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, $address );
        $this->pdf->SetX( 115 );
        $this->pdf->Cell( 0, 12 * $point, 'PO Box 353', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, $city . ', ' . $state . ( $zip && $zip != '' ? ' ' . $zip : '' ) );
        $this->pdf->SetX( 115 );
        $this->pdf->Cell( 0, 12 * $point, 'Long Lake, MN 55356', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ' ', 0, 1, 'C' );

        $this->pdf->Cell( 0, 12 * $point, 'Office: ' . ( $phone && $phone != '' ? $phone : '' ) );
        $this->pdf->SetX( 115 );
        $this->pdf->Cell( 0, 12 * $point, 'Office: (952) 448-4202', 0, 1 );

        $this->pdf->Cell( 0, 12 * $point, ( $website && $website != '' ? $website : 'Website:' ) );
        $this->pdf->SetX( 115 );
        $this->pdf->Cell( 0, 12 * $point, 'Fax:     (952) 368-7281', 0, 1 );
        
        $this->pdf->SetX( 115 );
        $this->pdf->Cell( 0, 12 * $point, 'www.timemusicagency.com', 0, 1 );

        $this->pdf->AliasNbPages();
        $this->pdf->last_page = true;

        // Output the generated PDF
        $this->pdf->Output( $this->file, 'F' );
    }
}
