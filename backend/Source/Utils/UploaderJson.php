<?php

class UploaderJson
{
    private int $minSize;
    private int $maxSize;
    private array $allowedTypes;
    private string $uploadDir;

    public function __construct(int $minSize, int $maxSize, array $allowedTypes, string $uploadDir = "uploads")
    {
        $this->minSize = $minSize;
        $this->maxSize = $maxSize;
        $this->allowedTypes = $allowedTypes;
        $this->uploadDir = rtrim($uploadDir, "/");

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    public function uploadFromJson(string $json): string
    {
        $data = json_decode($json, true);

        if (!$data || !isset($data['filename'], $data['data'])) {
            throw new Exception("JSON inválido. Esperado: { filename, data }");
        }

        $fileExt = strtolower(pathinfo($data['filename'], PATHINFO_EXTENSION));
        if (!in_array($fileExt, $this->allowedTypes)) {
            throw new Exception("Tipo de arquivo não permitido. Permitidos: " . implode(", ", $this->allowedTypes));
        }

        $binary = base64_decode($data['data'], true);
        if ($binary === false) {
            throw new Exception("Falha ao decodificar Base64.");
        }

        $fileSize = strlen($binary);
        if ($fileSize < $this->minSize) {
            throw new Exception("Arquivo muito pequeno. Tamanho mínimo: {$this->minSize} bytes");
        }
        if ($fileSize > $this->maxSize) {
            throw new Exception("Arquivo muito grande. Tamanho máximo: {$this->maxSize} bytes");
        }

        $newName = uniqid("img_", true) . "." . $fileExt;
        $destination = $this->uploadDir . "/" . $newName;

        if (file_put_contents($destination, $binary) === false) {
            throw new Exception("Erro ao salvar arquivo.");
        }

        return $destination;
    }
}
