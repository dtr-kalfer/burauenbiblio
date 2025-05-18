require_once(REL(__FILE__, "../model/MemberCustomFields_DM.php"));

class MemberCustomFields extends CoreTable {
    public $validCodes = [];

    public function __construct() {
        parent::__construct();
        $this->setName('member_custom_fields');
        $this->setFields(array(
            'mbrid'=>'string',
            'code'=>'string',
            'data'=>'string',
        ));
        $this->setKey('mbrid', 'code');
        $this->setForeignKey('mbrid', 'member', 'mbrid');

        // Load valid field codes from member_fields_dm
        $dm = new MemberCustomFields_DM();
        $rows = $dm->getAll();
        foreach ($rows as $row) {
            $this->validCodes[] = $row['code'];
        }
    }
