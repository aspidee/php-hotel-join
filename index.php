<?php

  include "databaseInfo.php";

  class Prenotazione {

    private $id;
    private $stanza_id;
    private $configurazione_id;
    private $created_at;

    public function __construct($id, $stanza_id, $configurazione_id, $created_at) {

      $this->id = $id;
      $this->stanza_id = $stanza_id;
      $this->configurazione_id = $configurazione_id;
      $this->created_at = $created_at;
    }

    function getId() {

      return $this->id;
    }
    function getStanzaId() {

      return $this->stanza_id;
    }

    function getConfigurazioneId() {

      return $this->configurazione_id;
    }

    public static function getAllPrenotazioni($conn) {

      $sql = "
              SELECT *
              FROM prenotazioni
              WHERE created_at >= '2018-05-01'
              AND created_at <= '2018-05-31'
              ORDER BY created_at DESC
      ";


      $result = $conn->query($sql);

      // var_dump($sql); die();

      if ($result->num_rows > 0) {
        $prenotazioni = [];
        while($row = $result->fetch_assoc()) {
          $prenotazioni[] =
              new Prenotazione($row["id"],
                               $row["stanza_id"],
                               $row["configurazione_id"],
                               $row["created_at"]);
        }
      }

      return $prenotazioni;
    }
  }

  class Stanza {

    private $id;
    private $room_number;
    private $floor;
    private $beds;

    function __construct($id, $room_number, $floor, $beds) {

      $this->id = $id;
      $this->room_number = $room_number;
      $this->floor = $floor;
      $this->beds = $beds;
    }

    function getRoomNumber() {

      return $this->room_number;
    }
    function getFloor() {

      return $this->floor;
    }

    public static function getStanzaById($conn, $id) {

      $sql = "
              SELECT *
              FROM stanze
              WHERE id = $id
      ";

      $result = $conn->query($sql);

      // var_dump($sql); die();

      if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stanza = new Stanza(
                      $row["id"],
                      $row["room_number"],
                      $row["floor"],
                      $row["beds"]);

        return $stanza;
      }
    }
  }

  class Configurazione {

    private $title;
    private $id;
    private $description;

    function __construct($id, $title, $description ) {

      $this->id = $id;
      $this->description = $description;
      $this->title = $title;
    }

    function getDescription() {

      return $this->description;
    }
    function getTitle() {

      return $this->title;
    }

    public static function getConfigurazioneById($conn, $id) {

      $sql = "
              SELECT *
              FROM configurazioni
              WHERE id = $id
      ";

      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $configurazione = new Configurazione(
                      $row["id"],
                      $row["title"],
                      $row["description"]);

        return $configurazione;
      }
    }
  }

  class Pagamento {

    private $id;
    private $status;
    private $price;

    function __construct($id, $status, $price) {

      $this->id = $id;
      $this->status = $status;
      $this->price = $price;
    }

    function getStatus() {

      return $this->status;
    }
    function getPrice() {

      return $this->price;
    }

    function getPagamentoById($conn, $id) {

      $sql= "
             SELECT *
             FROM pagamenti
             WHERE prenotazione_id = $id
      ";

      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $pagamento = new Pagamento(
                            $row["id"],
                            $row["status"],
                            $row["price"]);

        return $pagamento;
      }
    }
  }

  class Ospite {

    private $id;
    private $name;
    private $lastname;
    private $ospite_id;
    private $prenotazione_id;

    function __construct($id, $name, $lastname, $ospite_id, $prenotazione_id) {

      $this->$id = $id;
      $this->$name = $name;
      $this->$lastname = $lastname;
      $this->$ospite_id = $ospite_id;
      $this->$prenotazione_id = $prenotazione_id;
    }

    public static function getOspiteById($conn, $id) {

      $sql = "
      ";
      // SELECT *
      // FROM prenotazioni_has_ospiti
      // JOIN prenotazioni
      // ON prenotazioni_has_ospiti.prenotazione_id = prenotazioni.id
      // JOIN ospiti
      // ON prenotazioni_has_ospiti.ospite_id = ospiti.id

      // WHERE prenotazioni_has_ospiti.ospite_id = $id
      // $sql = "
      //         SELECT *
      //         FROM prenotazioni_has_ospiti
      //         WHERE id = $id
      // ";

      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $ospite = new Ospite(
                            $row["id"],
                            $row["name"],
                            $row["lastname"],
                            $row["ospite_id"],
                            $row["prenotazione_id"]);

        return $ospite;
      }
    }
  }



  $conn = new mysqli($servername, $username, $password, $dbname);

  if ($conn->connect_errno) {

    echo $conn->connect_error;
    return;
  }

  // var_dump($conn); die();

  $prenotazioni = Prenotazione::getAllPrenotazioni($conn);

  foreach ($prenotazioni as $prenotazione) {

    $stanza_id = $prenotazione->getStanzaId();
    $configurazione_id = $prenotazione->getConfigurazioneId();
    $pagamento_id = $prenotazione->getId();

    $stanza = Stanza::getStanzaById($conn, $stanza_id);
    $configurazione = Configurazione::getConfigurazioneById($conn, $configurazione_id);
    $pagamento = Pagamento::getPagamentoById($conn, $pagamento_id);
    $ospiti = Ospite::getOspiteById($conn, $id);

    echo "Prenotazione ID: " . $prenotazione->getId() . "<br>" .
         "Stanza nr : " . $stanza->getRoomNumber() . "<br>" .
         "Piano nr : " . $stanza->getFloor() . "<br>" .
         "Description: " . $configurazione->getDescription() . "<br>" .
         "Title: " . $configurazione->getTitle(). "<br>" .
         "Status Pagamento: " . $pagamento->getStatus() . "<br>" .
         "Price: " . $pagamento->getPrice() . "<br><hr>";
  }
  // "<br>" .
  // "Ospite" . $ospite->getOspiteById() .
 ?>
