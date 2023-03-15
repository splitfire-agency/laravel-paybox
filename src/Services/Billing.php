<?php

namespace Sf\PayboxGateway\Services;

use SimpleXMLElement;

class Billing
{
  /**
   * @var string|null $companyName
   */
  protected ?string $companyName = null;

  /**
   * @var string|null $firstName
   */
  protected ?string $firstName = null;

  /**
   * @var string|null $lastName
   */
  protected ?string $lastName = null;

  /**
   * @var string|null $address1
   */
  protected ?string $address1 = null;

  /**
   * @var string|null $address2
   */
  protected ?string $address2 = null;

  /**
   * @var string|null $zipcode
   */
  protected ?string $zipcode = null;

  /**
   * @var string|null $city
   */
  protected ?string $city = null;

  /**
   * @var int|null $countryCode
   */
  protected ?int $countryCode = null;

  protected static array $countries = [
    ["alpha2" => "AF", "alpha3" => "AFG", "numeric" => "004"],
    ["alpha2" => "AL", "alpha3" => "ALB", "numeric" => "008"],
    ["alpha2" => "DZ", "alpha3" => "DZA", "numeric" => "012"],
    ["alpha2" => "AS", "alpha3" => "ASM", "numeric" => "016"],
    ["alpha2" => "AD", "alpha3" => "AND", "numeric" => "020"],
    ["alpha2" => "AO", "alpha3" => "AGO", "numeric" => "024"],
    ["alpha2" => "AI", "alpha3" => "AIA", "numeric" => "660"],
    ["alpha2" => "AQ", "alpha3" => "ATA", "numeric" => "010"],
    ["alpha2" => "AG", "alpha3" => "ATG", "numeric" => "028"],
    ["alpha2" => "AR", "alpha3" => "ARG", "numeric" => "032"],
    ["alpha2" => "AM", "alpha3" => "ARM", "numeric" => "051"],
    ["alpha2" => "AW", "alpha3" => "ABW", "numeric" => "533"],
    ["alpha2" => "AU", "alpha3" => "AUS", "numeric" => "036"],
    ["alpha2" => "AT", "alpha3" => "AUT", "numeric" => "040"],
    ["alpha2" => "AZ", "alpha3" => "AZE", "numeric" => "031"],
    ["alpha2" => "BS", "alpha3" => "BHS", "numeric" => "044"],
    ["alpha2" => "BH", "alpha3" => "BHR", "numeric" => "048"],
    ["alpha2" => "BD", "alpha3" => "BGD", "numeric" => "050"],
    ["alpha2" => "BB", "alpha3" => "BRB", "numeric" => "052"],
    ["alpha2" => "BY", "alpha3" => "BLR", "numeric" => "112"],
    ["alpha2" => "BE", "alpha3" => "BEL", "numeric" => "056"],
    ["alpha2" => "BZ", "alpha3" => "BLZ", "numeric" => "084"],
    ["alpha2" => "BJ", "alpha3" => "BEN", "numeric" => "204"],
    ["alpha2" => "BM", "alpha3" => "BMU", "numeric" => "060"],
    ["alpha2" => "BT", "alpha3" => "BTN", "numeric" => "064"],
    ["alpha2" => "BO", "alpha3" => "BOL", "numeric" => "068"],
    ["alpha2" => "BQ", "alpha3" => "BES", "numeric" => "535"],
    ["alpha2" => "BA", "alpha3" => "BIH", "numeric" => "070"],
    ["alpha2" => "BW", "alpha3" => "BWA", "numeric" => "072"],
    ["alpha2" => "BV", "alpha3" => "BVT", "numeric" => "074"],
    ["alpha2" => "BR", "alpha3" => "BRA", "numeric" => "076"],
    ["alpha2" => "IO", "alpha3" => "IOT", "numeric" => "086"],
    ["alpha2" => "BN", "alpha3" => "BRN", "numeric" => "096"],
    ["alpha2" => "BG", "alpha3" => "BGR", "numeric" => "100"],
    ["alpha2" => "BF", "alpha3" => "BFA", "numeric" => "854"],
    ["alpha2" => "BI", "alpha3" => "BDI", "numeric" => "108"],
    ["alpha2" => "CV", "alpha3" => "CPV", "numeric" => "132"],
    ["alpha2" => "KH", "alpha3" => "KHM", "numeric" => "116"],
    ["alpha2" => "CM", "alpha3" => "CMR", "numeric" => "120"],
    ["alpha2" => "CA", "alpha3" => "CAN", "numeric" => "124"],
    ["alpha2" => "KY", "alpha3" => "CYM", "numeric" => "136"],
    ["alpha2" => "CF", "alpha3" => "CAF", "numeric" => "140"],
    ["alpha2" => "TD", "alpha3" => "TCD", "numeric" => "148"],
    ["alpha2" => "CL", "alpha3" => "CHL", "numeric" => "152"],
    ["alpha2" => "CN", "alpha3" => "CHN", "numeric" => "156"],
    ["alpha2" => "CX", "alpha3" => "CXR", "numeric" => "162"],
    ["alpha2" => "CC", "alpha3" => "CCK", "numeric" => "166"],
    ["alpha2" => "CO", "alpha3" => "COL", "numeric" => "170"],
    ["alpha2" => "KM", "alpha3" => "COM", "numeric" => "174"],
    ["alpha2" => "CD", "alpha3" => "COD", "numeric" => "180"],
    ["alpha2" => "CG", "alpha3" => "COG", "numeric" => "178"],
    ["alpha2" => "CK", "alpha3" => "COK", "numeric" => "184"],
    ["alpha2" => "CR", "alpha3" => "CRI", "numeric" => "188"],
    ["alpha2" => "HR", "alpha3" => "HRV", "numeric" => "191"],
    ["alpha2" => "CU", "alpha3" => "CUB", "numeric" => "192"],
    ["alpha2" => "CW", "alpha3" => "CUW", "numeric" => "531"],
    ["alpha2" => "CY", "alpha3" => "CYP", "numeric" => "196"],
    ["alpha2" => "CZ", "alpha3" => "CZE", "numeric" => "203"],
    ["alpha2" => "CI", "alpha3" => "CIV", "numeric" => "384"],
    ["alpha2" => "DK", "alpha3" => "DNK", "numeric" => "208"],
    ["alpha2" => "DJ", "alpha3" => "DJI", "numeric" => "262"],
    ["alpha2" => "DM", "alpha3" => "DMA", "numeric" => "212"],
    ["alpha2" => "DO", "alpha3" => "DOM", "numeric" => "214"],
    ["alpha2" => "EC", "alpha3" => "ECU", "numeric" => "218"],
    ["alpha2" => "EG", "alpha3" => "EGY", "numeric" => "818"],
    ["alpha2" => "SV", "alpha3" => "SLV", "numeric" => "222"],
    ["alpha2" => "GQ", "alpha3" => "GNQ", "numeric" => "226"],
    ["alpha2" => "ER", "alpha3" => "ERI", "numeric" => "232"],
    ["alpha2" => "EE", "alpha3" => "EST", "numeric" => "233"],
    ["alpha2" => "SZ", "alpha3" => "SWZ", "numeric" => "748"],
    ["alpha2" => "ET", "alpha3" => "ETH", "numeric" => "231"],
    ["alpha2" => "FK", "alpha3" => "FLK", "numeric" => "238"],
    ["alpha2" => "FO", "alpha3" => "FRO", "numeric" => "234"],
    ["alpha2" => "FJ", "alpha3" => "FJI", "numeric" => "242"],
    ["alpha2" => "FI", "alpha3" => "FIN", "numeric" => "246"],
    ["alpha2" => "FR", "alpha3" => "FRA", "numeric" => "250"],
    ["alpha2" => "GF", "alpha3" => "GUF", "numeric" => "254"],
    ["alpha2" => "PF", "alpha3" => "PYF", "numeric" => "258"],
    ["alpha2" => "TF", "alpha3" => "ATF", "numeric" => "260"],
    ["alpha2" => "GA", "alpha3" => "GAB", "numeric" => "266"],
    ["alpha2" => "GM", "alpha3" => "GMB", "numeric" => "270"],
    ["alpha2" => "GE", "alpha3" => "GEO", "numeric" => "268"],
    ["alpha2" => "DE", "alpha3" => "DEU", "numeric" => "276"],
    ["alpha2" => "GH", "alpha3" => "GHA", "numeric" => "288"],
    ["alpha2" => "GI", "alpha3" => "GIB", "numeric" => "292"],
    ["alpha2" => "GR", "alpha3" => "GRC", "numeric" => "300"],
    ["alpha2" => "GL", "alpha3" => "GRL", "numeric" => "304"],
    ["alpha2" => "GD", "alpha3" => "GRD", "numeric" => "308"],
    ["alpha2" => "GP", "alpha3" => "GLP", "numeric" => "312"],
    ["alpha2" => "GU", "alpha3" => "GUM", "numeric" => "316"],
    ["alpha2" => "GT", "alpha3" => "GTM", "numeric" => "320"],
    ["alpha2" => "GG", "alpha3" => "GGY", "numeric" => "831"],
    ["alpha2" => "GN", "alpha3" => "GIN", "numeric" => "324"],
    ["alpha2" => "GW", "alpha3" => "GNB", "numeric" => "624"],
    ["alpha2" => "GY", "alpha3" => "GUY", "numeric" => "328"],
    ["alpha2" => "HT", "alpha3" => "HTI", "numeric" => "332"],
    ["alpha2" => "HM", "alpha3" => "HMD", "numeric" => "334"],
    ["alpha2" => "VA", "alpha3" => "VAT", "numeric" => "336"],
    ["alpha2" => "HN", "alpha3" => "HND", "numeric" => "340"],
    ["alpha2" => "HK", "alpha3" => "HKG", "numeric" => "344"],
    ["alpha2" => "HU", "alpha3" => "HUN", "numeric" => "348"],
    ["alpha2" => "IS", "alpha3" => "ISL", "numeric" => "352"],
    ["alpha2" => "IN", "alpha3" => "IND", "numeric" => "356"],
    ["alpha2" => "ID", "alpha3" => "IDN", "numeric" => "360"],
    ["alpha2" => "IR", "alpha3" => "IRN", "numeric" => "364"],
    ["alpha2" => "IQ", "alpha3" => "IRQ", "numeric" => "368"],
    ["alpha2" => "IE", "alpha3" => "IRL", "numeric" => "372"],
    ["alpha2" => "IM", "alpha3" => "IMN", "numeric" => "833"],
    ["alpha2" => "IL", "alpha3" => "ISR", "numeric" => "376"],
    ["alpha2" => "IT", "alpha3" => "ITA", "numeric" => "380"],
    ["alpha2" => "JM", "alpha3" => "JAM", "numeric" => "388"],
    ["alpha2" => "JP", "alpha3" => "JPN", "numeric" => "392"],
    ["alpha2" => "JE", "alpha3" => "JEY", "numeric" => "832"],
    ["alpha2" => "JO", "alpha3" => "JOR", "numeric" => "400"],
    ["alpha2" => "KZ", "alpha3" => "KAZ", "numeric" => "398"],
    ["alpha2" => "KE", "alpha3" => "KEN", "numeric" => "404"],
    ["alpha2" => "KI", "alpha3" => "KIR", "numeric" => "296"],
    ["alpha2" => "KP", "alpha3" => "PRK", "numeric" => "408"],
    ["alpha2" => "KR", "alpha3" => "KOR", "numeric" => "410"],
    ["alpha2" => "KW", "alpha3" => "KWT", "numeric" => "414"],
    ["alpha2" => "KG", "alpha3" => "KGZ", "numeric" => "417"],
    ["alpha2" => "LA", "alpha3" => "LAO", "numeric" => "418"],
    ["alpha2" => "LV", "alpha3" => "LVA", "numeric" => "428"],
    ["alpha2" => "LB", "alpha3" => "LBN", "numeric" => "422"],
    ["alpha2" => "LS", "alpha3" => "LSO", "numeric" => "426"],
    ["alpha2" => "LR", "alpha3" => "LBR", "numeric" => "430"],
    ["alpha2" => "LY", "alpha3" => "LBY", "numeric" => "434"],
    ["alpha2" => "LI", "alpha3" => "LIE", "numeric" => "438"],
    ["alpha2" => "LT", "alpha3" => "LTU", "numeric" => "440"],
    ["alpha2" => "LU", "alpha3" => "LUX", "numeric" => "442"],
    ["alpha2" => "MO", "alpha3" => "MAC", "numeric" => "446"],
    ["alpha2" => "MG", "alpha3" => "MDG", "numeric" => "450"],
    ["alpha2" => "MW", "alpha3" => "MWI", "numeric" => "454"],
    ["alpha2" => "MY", "alpha3" => "MYS", "numeric" => "458"],
    ["alpha2" => "MV", "alpha3" => "MDV", "numeric" => "462"],
    ["alpha2" => "ML", "alpha3" => "MLI", "numeric" => "466"],
    ["alpha2" => "MT", "alpha3" => "MLT", "numeric" => "470"],
    ["alpha2" => "MH", "alpha3" => "MHL", "numeric" => "584"],
    ["alpha2" => "MQ", "alpha3" => "MTQ", "numeric" => "474"],
    ["alpha2" => "MR", "alpha3" => "MRT", "numeric" => "478"],
    ["alpha2" => "MU", "alpha3" => "MUS", "numeric" => "480"],
    ["alpha2" => "YT", "alpha3" => "MYT", "numeric" => "175"],
    ["alpha2" => "MX", "alpha3" => "MEX", "numeric" => "484"],
    ["alpha2" => "FM", "alpha3" => "FSM", "numeric" => "583"],
    ["alpha2" => "MD", "alpha3" => "MDA", "numeric" => "498"],
    ["alpha2" => "MC", "alpha3" => "MCO", "numeric" => "492"],
    ["alpha2" => "MN", "alpha3" => "MNG", "numeric" => "496"],
    ["alpha2" => "ME", "alpha3" => "MNE", "numeric" => "499"],
    ["alpha2" => "MS", "alpha3" => "MSR", "numeric" => "500"],
    ["alpha2" => "MA", "alpha3" => "MAR", "numeric" => "504"],
    ["alpha2" => "MZ", "alpha3" => "MOZ", "numeric" => "508"],
    ["alpha2" => "MM", "alpha3" => "MMR", "numeric" => "104"],
    ["alpha2" => "NA", "alpha3" => "NAM", "numeric" => "516"],
    ["alpha2" => "NR", "alpha3" => "NRU", "numeric" => "520"],
    ["alpha2" => "NP", "alpha3" => "NPL", "numeric" => "524"],
    ["alpha2" => "NL", "alpha3" => "NLD", "numeric" => "528"],
    ["alpha2" => "NC", "alpha3" => "NCL", "numeric" => "540"],
    ["alpha2" => "NZ", "alpha3" => "NZL", "numeric" => "554"],
    ["alpha2" => "NI", "alpha3" => "NIC", "numeric" => "558"],
    ["alpha2" => "NE", "alpha3" => "NER", "numeric" => "562"],
    ["alpha2" => "NG", "alpha3" => "NGA", "numeric" => "566"],
    ["alpha2" => "NU", "alpha3" => "NIU", "numeric" => "570"],
    ["alpha2" => "NF", "alpha3" => "NFK", "numeric" => "574"],
    ["alpha2" => "MP", "alpha3" => "MNP", "numeric" => "580"],
    ["alpha2" => "NO", "alpha3" => "NOR", "numeric" => "578"],
    ["alpha2" => "OM", "alpha3" => "OMN", "numeric" => "512"],
    ["alpha2" => "PK", "alpha3" => "PAK", "numeric" => "586"],
    ["alpha2" => "PW", "alpha3" => "PLW", "numeric" => "585"],
    ["alpha2" => "PS", "alpha3" => "PSE", "numeric" => "275"],
    ["alpha2" => "PA", "alpha3" => "PAN", "numeric" => "591"],
    ["alpha2" => "PG", "alpha3" => "PNG", "numeric" => "598"],
    ["alpha2" => "PY", "alpha3" => "PRY", "numeric" => "600"],
    ["alpha2" => "PE", "alpha3" => "PER", "numeric" => "604"],
    ["alpha2" => "PH", "alpha3" => "PHL", "numeric" => "608"],
    ["alpha2" => "PN", "alpha3" => "PCN", "numeric" => "612"],
    ["alpha2" => "PL", "alpha3" => "POL", "numeric" => "616"],
    ["alpha2" => "PT", "alpha3" => "PRT", "numeric" => "620"],
    ["alpha2" => "PR", "alpha3" => "PRI", "numeric" => "630"],
    ["alpha2" => "QA", "alpha3" => "QAT", "numeric" => "634"],
    ["alpha2" => "MK", "alpha3" => "MKD", "numeric" => "807"],
    ["alpha2" => "RO", "alpha3" => "ROU", "numeric" => "642"],
    ["alpha2" => "RU", "alpha3" => "RUS", "numeric" => "643"],
    ["alpha2" => "RW", "alpha3" => "RWA", "numeric" => "646"],
    ["alpha2" => "RE", "alpha3" => "REU", "numeric" => "638"],
    ["alpha2" => "BL", "alpha3" => "BLM", "numeric" => "652"],
    ["alpha2" => "SH", "alpha3" => "SHN", "numeric" => "654"],
    ["alpha2" => "KN", "alpha3" => "KNA", "numeric" => "659"],
    ["alpha2" => "LC", "alpha3" => "LCA", "numeric" => "662"],
    ["alpha2" => "MF", "alpha3" => "MAF", "numeric" => "663"],
    ["alpha2" => "PM", "alpha3" => "SPM", "numeric" => "666"],
    ["alpha2" => "VC", "alpha3" => "VCT", "numeric" => "670"],
    ["alpha2" => "WS", "alpha3" => "WSM", "numeric" => "882"],
    ["alpha2" => "SM", "alpha3" => "SMR", "numeric" => "674"],
    ["alpha2" => "ST", "alpha3" => "STP", "numeric" => "678"],
    ["alpha2" => "SA", "alpha3" => "SAU", "numeric" => "682"],
    ["alpha2" => "SN", "alpha3" => "SEN", "numeric" => "686"],
    ["alpha2" => "RS", "alpha3" => "SRB", "numeric" => "688"],
    ["alpha2" => "SC", "alpha3" => "SYC", "numeric" => "690"],
    ["alpha2" => "SL", "alpha3" => "SLE", "numeric" => "694"],
    ["alpha2" => "SG", "alpha3" => "SGP", "numeric" => "702"],
    ["alpha2" => "SX", "alpha3" => "SXM", "numeric" => "534"],
    ["alpha2" => "SK", "alpha3" => "SVK", "numeric" => "703"],
    ["alpha2" => "SI", "alpha3" => "SVN", "numeric" => "705"],
    ["alpha2" => "SB", "alpha3" => "SLB", "numeric" => "090"],
    ["alpha2" => "SO", "alpha3" => "SOM", "numeric" => "706"],
    ["alpha2" => "ZA", "alpha3" => "ZAF", "numeric" => "710"],
    ["alpha2" => "GS", "alpha3" => "SGS", "numeric" => "239"],
    ["alpha2" => "SS", "alpha3" => "SSD", "numeric" => "728"],
    ["alpha2" => "ES", "alpha3" => "ESP", "numeric" => "724"],
    ["alpha2" => "LK", "alpha3" => "LKA", "numeric" => "144"],
    ["alpha2" => "SD", "alpha3" => "SDN", "numeric" => "729"],
    ["alpha2" => "SR", "alpha3" => "SUR", "numeric" => "740"],
    ["alpha2" => "SJ", "alpha3" => "SJM", "numeric" => "744"],
    ["alpha2" => "SE", "alpha3" => "SWE", "numeric" => "752"],
    ["alpha2" => "CH", "alpha3" => "CHE", "numeric" => "756"],
    ["alpha2" => "SY", "alpha3" => "SYR", "numeric" => "760"],
    ["alpha2" => "TW", "alpha3" => "TWN", "numeric" => "158"],
    ["alpha2" => "TJ", "alpha3" => "TJK", "numeric" => "762"],
    ["alpha2" => "TZ", "alpha3" => "TZA", "numeric" => "834"],
    ["alpha2" => "TH", "alpha3" => "THA", "numeric" => "764"],
    ["alpha2" => "TL", "alpha3" => "TLS", "numeric" => "626"],
    ["alpha2" => "TG", "alpha3" => "TGO", "numeric" => "768"],
    ["alpha2" => "TK", "alpha3" => "TKL", "numeric" => "772"],
    ["alpha2" => "TO", "alpha3" => "TON", "numeric" => "776"],
    ["alpha2" => "TT", "alpha3" => "TTO", "numeric" => "780"],
    ["alpha2" => "TN", "alpha3" => "TUN", "numeric" => "788"],
    ["alpha2" => "TR", "alpha3" => "TUR", "numeric" => "792"],
    ["alpha2" => "TM", "alpha3" => "TKM", "numeric" => "795"],
    ["alpha2" => "TC", "alpha3" => "TCA", "numeric" => "796"],
    ["alpha2" => "TV", "alpha3" => "TUV", "numeric" => "798"],
    ["alpha2" => "UG", "alpha3" => "UGA", "numeric" => "800"],
    ["alpha2" => "UA", "alpha3" => "UKR", "numeric" => "804"],
    ["alpha2" => "AE", "alpha3" => "ARE", "numeric" => "784"],
    ["alpha2" => "GB", "alpha3" => "GBR", "numeric" => "826"],
    ["alpha2" => "UM", "alpha3" => "UMI", "numeric" => "581"],
    ["alpha2" => "US", "alpha3" => "USA", "numeric" => "840"],
    ["alpha2" => "UY", "alpha3" => "URY", "numeric" => "858"],
    ["alpha2" => "UZ", "alpha3" => "UZB", "numeric" => "860"],
    ["alpha2" => "VU", "alpha3" => "VUT", "numeric" => "548"],
    ["alpha2" => "VE", "alpha3" => "VEN", "numeric" => "862"],
    ["alpha2" => "VN", "alpha3" => "VNM", "numeric" => "704"],
    ["alpha2" => "VG", "alpha3" => "VGB", "numeric" => "092"],
    ["alpha2" => "VI", "alpha3" => "VIR", "numeric" => "850"],
    ["alpha2" => "WF", "alpha3" => "WLF", "numeric" => "876"],
    ["alpha2" => "EH", "alpha3" => "ESH", "numeric" => "732"],
    ["alpha2" => "YE", "alpha3" => "YEM", "numeric" => "887"],
    ["alpha2" => "ZM", "alpha3" => "ZMB", "numeric" => "894"],
    ["alpha2" => "ZW", "alpha3" => "ZWE", "numeric" => "716"],
    ["alpha2" => "AX", "alpha3" => "ALA", "numeric" => "248"],
  ];

  /**
   * Create billing object
   * @param array $attributes
   */
  public function __construct(array $attributes = [])
  {
    foreach ($attributes as $attribute => $value) {
      $setter = "set" . ucfirst($attribute);
      if (method_exists($this, $setter)) {
        $this->$setter($value);
      }
    }
  }

  /**
   * Get Billing XML
   * @return string
   */
  public function getXml(): string
  {
    $dom = new SimpleXMLElement(
      '<?xml version="1.0" encoding="utf-8"?><Billing><Address></Address></Billing>'
    );
    if ($this->companyName) {
      $dom->addChild("CompanyName", $this->companyName);
    }
    $dom->addChild("FirstName", $this->firstName ?? "xxx");
    $dom->addChild("LastName", $this->lastName ?? "xxx");
    $dom->addChild("Address1", $this->address1 ?? "xxx");
    if ($this->address2) {
      $dom->addChild("Address2", $this->address2);
    }
    $dom->addChild("ZipCode", $this->zipcode ?? "xxx");
    $dom->addChild("City", $this->city ?? "xxx");
    $dom->addChild("CountryCode", $this->countryCode ?? 250);

    return $dom->asXML();
  }

  /**
   * @return string|null
   */
  public function getCompanyName(): ?string
  {
    return $this->companyName;
  }

  /**
   * @param string|null $companyName
   * @return self
   */
  public function setCompanyName(?string $companyName): self
  {
    $this->companyName = $companyName;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getFirstName(): ?string
  {
    return $this->firstName;
  }

  /**
   * @param string|null $firstName
   * @return self
   */
  public function setFirstName(?string $firstName): self
  {
    $this->firstName = $firstName;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getLastName(): ?string
  {
    return $this->lastName;
  }

  /**
   * @param string|null $lastName
   * @return self
   */
  public function setLastName(?string $lastName): self
  {
    $this->lastName = $lastName;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getAddress1(): ?string
  {
    return $this->address1;
  }

  /**
   * @param string|null $address1
   * @return self
   */
  public function setAddress1(?string $address1): self
  {
    $this->address1 = $address1;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getAddress2(): ?string
  {
    return $this->address2;
  }

  /**
   * @param string|null $address2
   * @return self
   */
  public function setAddress2(?string $address2): self
  {
    $this->address2 = $address2;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getZipcode(): ?string
  {
    return $this->zipcode;
  }

  /**
   * @param string|null $zipcode
   * @return self
   */
  public function setZipcode(?string $zipcode): self
  {
    $this->zipcode = $zipcode;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getCity(): ?string
  {
    return $this->city;
  }

  /**
   * @param string|null $city
   * @return self
   */
  public function setCity(?string $city): self
  {
    $this->city = $city;
    return $this;
  }

  /**
   * @return int|null
   */
  public function getCountryCode(): ?int
  {
    return $this->countryCode;
  }

  /**
   * @param string|null $countryCode
   * @return self
   */
  public function setCountryCode(?string $countryCode): self
  {
    if (null !== $countryCode && preg_match('/^[a-zA-Z]{2}$/', $countryCode)) {
      $this->setCountryCodeFromAlpha2($countryCode);
    } elseif (
      null !== $countryCode &&
      preg_match('/^[a-zA-Z]{3}$/', $countryCode)
    ) {
      $this->setCountryCodeFromAlpha3($countryCode);
    } else {
      $this->countryCode = $countryCode;
    }
    return $this;
  }

  /**
   * @param string $countryCode
   * @return self
   */
  public function setCountryCodeFromAlpha2(string $countryCode): self
  {
    $countryCode = strtoupper($countryCode);
    foreach (self::$countries as $country) {
      if ($country["alpha2"] == $countryCode) {
        $this->countryCode = $country["numeric"];
        break;
      }
    }
    return $this;
  }

  /**
   * @param string $countryCode
   * @return self
   */
  public function setCountryCodeFromAlpha3(string $countryCode): self
  {
    $countryCode = strtoupper($countryCode);
    foreach (self::$countries as $country) {
      if ($country["alpha3"] === $countryCode) {
        $this->countryCode = $country["numeric"];
        break;
      }
    }
    return $this;
  }
}
