<?php
namespace wcf\util;
use wcf\system\WCF;
use wcf\system\exception\SystemException;


/**
 * DatabaseObjectCreator short summary.
 *
 * DatabaseObjectCreator description.
 *
 * @version 1.0
 * @author jarau
 */
final class DatabaseObjectCreator {

    const LICENSE_LGPL = 0;
    const LICENSE_GPL = 1;
    const LICENSE_MIT = 2;
    const LICENSE_PD = 3;

    const ALLCLASSES = 1;
    const BASECLASS = 'base';
    const EDITCLASS = 'edit';
    const ACTIONCLASS = 'action';
    const DECORATEDCLASS = 'decorator';
    const LISTCLASS = 'list';

    //const WCF_DATABASE_OBJECT = 'use wcf\data\DatabaseObject;';
    //const WCF_DATABASE_OBJECT_ACTION = 'use wcf\data\AbstractDatabaseObjectAction;';
    //const WCF_DATABASE_OBJECT_EDITOR = 'use wcf\data\DatabaseObjectEditor;';
    //const WCF_DATABASE_OBJECT_LIST = 'use wcf\data\DatabaseObjectList;';

    private $tableName = '';
    private $tableKey = null;
    private $tableFields = null;

    private $className = '';
    private $namespace = '';

    private $license = '';
    private $commentHead = null;
    private $comment = '';
    private $package = '';
    private $author = '';
    private $category = '';

    private $objectName = '';
    private $absPath = '';


    private $licenseText = array(
        0 => " * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php> \r\n",
        1 => " * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php> \r\n",
        2 => " * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT> \r\n",
        3 => " * @license	Public Domain Dedication <https://creativecommons.org/publicdomain/zero/> \r\n",
        );

    private $includeArray = array(
            'base'      => "use wcf\data\DatabaseObject;\r\n",
            'edit'      => "use wcf\data\DatabaseObjectEditor;\r\n",
            'action'    => "use wcf\data\AbstractDatabaseObjectAction;\r\n",
            'decorator' => "use wcf\data\DatabaseObjectDecorator;\r\n",
            'list'      => "use wcf\data\DatabaseObjectList;\r\n",
    );

    private $extendArray = array(
            'base'      => 'DatabaseObject',
            'edit'      => 'DatabaseObjectEditor',
            'action'    => 'AbstractDatabaseObjectAction',
            'decorator' => 'DatabaseObjectDecorator',
            'list'      => 'DatabaseObjectList',
    );

    private $suffixArray = array(
            'base'      => '',
            'edit'      => 'Editor',
            'action'    => 'Action',
            'decorator' => 'Decorated',
            'list'      => 'List',
    );

    /**
     * prepares a WCF DBO
     *
     * @param	string		$tableName		Name of the SQL table
     * @param	string		$className		[optional] name of the base class. without suffix like List/Edit/Action. If empty tbalename will be used.
     * @param	string		$namespace		namespace of the DBOs app\data\<your_namespace> you mus escape any backslashes
     * @param	string		$pathToLib		path to your lib path e.g.: "/var/www/example_com/ecf/lib/"
     * @param	string		$apllictaion	[optional] the base application. If empty wcf will be used.
     */
	public function __construct($tableName, $className = '', $namespace, $pathToLib, $apllictaion = 'wcf') {
        $this->tableName = strpos($tableName, 'wcf'. WCF_N .'_')===false ?  'wcf'. WCF_N .'_'. $tableName : $tableName;
		$this->className = empty($className) ? self::getClassBaseName($this->tableName) : $className;
		$this->namespace = $apllictaion .'\\data\\' . $namespace;

        if (substr($pathToLib, -1)!= DIRECTORY_SEPARATOR) $pathToLib .= DIRECTORY_SEPARATOR;

        $this->absPath = $pathToLib . 'data' . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        self::analyseTableStructure($tableName);
        echo '<p><br/></p>DatabaseObjectCreator::INIT <br /><br /> Tabellenname: '.$this->tableName . '<br /> Klassenname: '.$this->className . '<br /> Namespace: '.$this->namespace . '<br /> Pfad: '.$this->absPath;
    }

    public function setObjectName($objectName) {
        $this->objectName = $objectName;
        $this->commentHead = array(
            'base' => "/**\r\n * Represents a ". $objectName."\r\n",
            'edit' => "/**\r\n * Provides functions to edit ".$objectName."s.\r\n",
            'action' => "/**\r\n * Executes ".$objectName."-related actions.\r\n",
            'list' => "/**\r\n * Represents a list of ".$objectName."s.\r\n",
            'decorator' => "/**\r\n * Provides methods for ".$objectName.".\r\n");

    }

    public function setComment($comment) {
        $this->comment .= "\r\n * ". StringUtil::splitIntoChunks($comment, 60, "\r\n * "). "\r\n";
    }

    public function setAuthor($author) {
        $this->author = " * @author\t". $author ."\r\n";
    }

    public function setCopyright($copyright) {
        $this->copyright = " * @copyright\t" . date("Y") . "  " . $copyright ."\r\n";
    }

    public function setLicense($license) {
        $this->license = is_int($license) ? $this->licenseText[$license] : " * @license\t" . $license ."\r\n";
    }

    public function setPackage($package) {
        $this->package = " * @package\t".$package ."\r\n";
    }

    public function setCategory($catagory) {
        $this->catagory = "* @category\t".$catagory ."\r\n";
    }

    private function createClassContetnt($type) {
        if (empty($this->objectName)) self::setObjectName(mb_strtolower($this->className));
        if (empty($this->author)) self::setAuthor('DBO Creator');
        if (empty($this->copyright)) self::setCopyright('DBO Creator');
        if (empty($this->license)) self::setLicense(0);
        if (empty($this->package)) self::setPackage('com.example.myplugin');
        if (empty($this->category)) self::setCategory('Woltlab Community Framework');
        $classContent = "<?php\r\n";
        $classContent .= 'namespace '.$this->namespace . ";\r\n";
        $classContent .= $this->includeArray[$type] . "\r\n";
        $classContent .= $this->commentHead[$type];
        $classContent .= $this->comment;
        $classContent .= $this->author;
        $classContent .= $this->copyright;
        $classContent .= $this->license;
        $classContent .= $this->package;
        $classContent .= $this->category;

        if ($type == self::BASECLASS) {
            $classContent .= " *\r\n * @property ". $this->tableKey["type"] ."\t\t $" . $this->tableKey["name"] ."\t\t\t PRIMARY KEY \r\n";
            foreach($this->tableFields as $property) {
                $classContent .= " * @property ". $property["type"] ."\t\t $" . $property["name"] ."\t\t\t" . $property["comment"] . "\r\n";
            }
        }

        $classContent .= " * \r\n */\r\n\r\n";
        $classContent .= 'class '.$this->className . $this->suffixArray[$type]. ' extends ' . $this->extendArray[$type] . ' {' ."\r\n";

        if ($type == self::BASECLASS) {
            $classContent .= "\t/**\r\n\t".' * {@inheritDoc}' ."\r\n\t */\r\n";
            $classContent .= "\t".'protected static $databaseTableName = \'' . str_replace('wcf'. WCF_N .'_', '', $this->tableName) .'\';'. "\r\n";
            $classContent .= "\t/**\r\n\t".' * {@inheritDoc}' ."\r\n\t */\r\n";
            $classContent .= "\t".'protected static $databaseTableIndexName = \'' .$this->tableKey['name'].'\';' ."\r\n";
        }

        if ($type == self::DECORATEDCLASS) {
            $classContent .= "\t/**\r\n\t".' * {@inheritDoc}' ."\r\n\t */\r\n";
            $classContent .= "\t".'public static $baseClass = ' .$this->className.'::class;' ."\r\n";
        }

        if ($type == self::EDITCLASS) {
            $classContent .= "\t/**\r\n\t".' * {@inheritDoc}'. "\r\n\t */\r\n";
            $classContent .= "\t".'public static $baseClass = ' .$this->className.'::class;' ."\r\n";
        }

       if ($type == self::ACTIONCLASS) {
                $classContent .= "\t/**\r\n\t".' * {@inheritDoc}'. "\r\n\t */\r\n";
                $classContent .= "\t".'public static $baseClass = ' .$this->className.'Editor::class;' ."\r\n";
                $classContent .= "\t/**\r\n\t".' * {@inheritDoc}'. "\r\n\t */\r\n";
                $classContent .= "\t".'protected $permissionsUpdate = array();'. "\r\n";
                $classContent .= "\t/**\r\n\t".' * {@inheritDoc}'. "\r\n\t */\r\n";
                $classContent .= "\t".'protected $permissionsCreate = array();'. "\r\n";
                $classContent .= "\t/**\r\n\t".' * {@inheritDoc}'. "\r\n\t */\r\n";
                $classContent .= "\t".'protected $permissionsDelete = array();'. "\r\n";
                $classContent .= "\t/**\r\n\t".' * {@inheritDoc}'. "\r\n\t */\r\n";
                $classContent .= "\t".'protected $requireACP = array();'. "\r\n";
                $classContent .= "\t/**\r\n\t".' * {@inheritDoc}'. "\r\n\t */\r\n";
                $classContent .= "\t".'protected $allowGuestAccess = array();'. "\r\n";
          }

            if ($type == self::LISTCLASS) {
                $classContent .= "\t/**\r\n\t".' * {@inheritDoc}'. "\r\n\t */\r\n";
                $classContent .= "\t".'public static $baseClass = ' .$this->className.'::class;' ."\r\n";
            }

            $classContent .= "\r\n}";
            return  $classContent;
    }



    /**
     * analyse the table to get informations about fields and the primary key
     *
     * @param	string		$tableName		Name of the SQL table
     */
    private function analyseTableStructure($tablename) {
        $key = null;
        $fields = array();
        $getType = function($val) {
            $val = mb_strtolower($val);
            if(strpos($val, 'int')!== false) return 'integer';
            if(strpos($val, 'varchar')!== false) return 'string';
            if(strpos($val, 'text')!== false) return 'string';
            if(strpos($val, 'date')!== false) return 'date';
            return 'string';
        };
        $sql = 'SHOW FULL columns FROM "'.$tablename.'"';
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
        while ($row = $statement->fetchArray()) {
            if ($row['Key'] == 'PRI') {
                $key = array(
                    'name'      => $row['Field'],
                    'type'      => $getType($row['Type']),
                    'comment'   => $row['Comment'],
                );
            }
            else {
                $fields[] = array(
                   'name'      => $row['Field'],
                   'type'      => $getType($row['Type']),
                   'comment'   => $row['Comment'],
               );
            }
        }
        $this->tableKey = $key;
        $this->tableFields= $fields;
    }

    private function getClassBaseName($table) {
        $tempArray  = explode('_', str_replace('wcf'. WCF_N .'_', '', $table));
        $classname = '';
        foreach($tempArray as $tempString) {
            $classname .= ucfirst($tempString);
        }
        return $classname;
    }

    public function execute($type) {
        if (!is_dir($this->absPath)) mkdir($this->absPath, 0777, true);
        $filename = "";
        echo "<br>Write clases for ".  $this->className ." to ";
        if ($type==self::ALLCLASSES) {
            // Create BaseClass
            $filename = $this->absPath . $this->className .'.class.php';
            $classContent = self::createClassContetnt(self::BASECLASS);
            if (file_put_contents($filename, $classContent)===false) throw new SystemException('Can not write file: '. $filename . ' Please check privileges');
            // Create DecoratedClass
            $filename = $this->absPath . $this->className . $this->suffixArray[self::DECORATEDCLASS] .'.class.php';
            $classContent = self::createClassContetnt(self::DECORATEDCLASS);
            if (file_put_contents($filename, $classContent)===false) throw new SystemException('Can not write file: '. $filename . ' Please check privileges');
            // Create ClassEditor
            $filename = $this->absPath . $this->className . $this->suffixArray[self::EDITCLASS] .'.class.php';
            $classContent = self::createClassContetnt(self::EDITCLASS);
            if (file_put_contents($filename, $classContent)===false) throw new SystemException('Can not write file: '. $filename . ' Please check privileges');
            // Create Class Action
            $filename = $this->absPath . $this->className . $this->suffixArray[self::ACTIONCLASS] .'.class.php';
            $classContent = self::createClassContetnt(self::ACTIONCLASS);
            if (file_put_contents($filename, $classContent)===false) throw new SystemException('Can not write file: '. $filename . ' Please check privileges');
            // Create Class List
            $filename = $this->absPath . $this->className . $this->suffixArray[self::LISTCLASS] .'.class.php';
            $classContent = self::createClassContetnt(self::LISTCLASS);
            if (file_put_contents($filename, $classContent)===false) throw new SystemException('Can not write file: '. $filename . ' Please check privileges');
        }
        elseif($type==self::BASECLASS) {
            $filename = $this->absPath . $this->className .'.class.php';
            $classContent = self::createClassContetnt(self::BASECLASS);
            if (file_put_contents($filename, $classContent)===false) throw new SystemException('Can not write file: '. $filename . ' Please check privileges');
        }
        else {
            if (!isset($this->suffixArray[$type])) throw new SystemException('Invalid DBO Type provided');
            $filename = $this->absPath . $this->className . $this->suffixArray[$type] .'.class.php';
            $classContent = self::createClassContetnt($type);
            if (file_put_contents($filename, $classContent)===false) throw new SystemException('Can not write file: '. $filename . ' Please check privileges');
        }
        echo $filename . "<br>";

    }

}