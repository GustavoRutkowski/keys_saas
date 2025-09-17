<?php

namespace Source\Utils;

use Exception;

class FileUploader
{
    private int $minSize;
    private int $maxSize;
    private array $allowedTypes;
    private string $prefix;
    private string $uploadDir;

    public function __construct(int $minSize, int $maxSize, array $allowedTypes, string $prefix = 'file_', string $uploadDir = "uploads")
    {
        $this->minSize = $minSize;
        $this->maxSize = $maxSize;
        $this->allowedTypes = $allowedTypes;
        $this->prefix = $prefix;
        $this->uploadDir = $this->resolveUploadDir($uploadDir);

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    private function resolveUploadDir(string $uploadDir): string
    {
        // Se já for um caminho absoluto, retorne diretamente
        if ($this->isAbsolutePath($uploadDir)) {
            return rtrim($uploadDir, "/");
        }

        // Resolve o caminho relativo em relação ao diretório da classe (__DIR__)
        $baseDir = __DIR__;
        $relativePath = $uploadDir;

        // Remove qualquer './' no início do caminho relativo
        if (strpos($relativePath, './') === 0) {
            $relativePath = substr($relativePath, 2);
        }

        // Resolve '../' para subir diretórios
        while (strpos($relativePath, '../') === 0) {
            $baseDir = dirname($baseDir);
            $relativePath = substr($relativePath, 3);
        }

        // Combina o diretório base com o caminho relativo restante
        return rtrim($baseDir . '/' . $relativePath, "/");
    }

    private function isAbsolutePath(string $path): bool
    {
        // Verifica se é um caminho absoluto no Windows (ex: C:\ ou C:/)
        if (preg_match('/^[A-Za-z]:[\/\\\\]/', $path)) {
            return true;
        }

        // Verifica se é um caminho absoluto no Linux (começa com /)
        return $path[0] === '/';
    }

    // Recebe um array assoc. no formato:
    /*
        [
            "filename" => "meu-arquivo.jpg",
            "data" => BASE64_DO_ARQUIVO
        ]
    */
    // Retorna o nome do arquivo salvo.
    public function uploadFile(array $data): string
    {
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
            throw new Exception("Arquivo muito pequeno ({$fileSize} bytes). Tamanho mínimo: {$this->minSize} bytes");
        }
        if ($fileSize > $this->maxSize) {
            throw new Exception("Arquivo muito grande. Tamanho máximo: {$this->maxSize} bytes");
        }

        $newName = uniqid($this->prefix, true) . "." . $fileExt;
        $destination = $this->uploadDir . "/" . $newName;

        if (file_put_contents($destination, $binary) === false) {
            throw new Exception("Erro ao salvar arquivo.");
        }

        return $newName;
    }

    public function removeFile(string $filename): bool
    {
        // Prevenir path traversal attacks - garantir que o arquivo está dentro do diretório de upload
        $realPath = realpath($this->uploadDir . '/' . $filename);
        $realUploadDir = realpath($this->uploadDir);

        // Verificar se o arquivo está dentro do diretório de upload
        if ($realPath === false || strpos($realPath, $realUploadDir) !== 0) {
            throw new Exception("Arquivo não encontrado ou fora do diretório de upload permitido.");
        }

        // Verificar se o arquivo existe e é um arquivo regular (não diretório)
        if (!is_file($realPath)) {
            throw new Exception("O arquivo especificado não existe.");
        }

        // Tentar remover o arquivo
        if (!unlink($realPath)) {
            throw new Exception("Falha ao remover o arquivo.");
        }

        return true;
    }
}
