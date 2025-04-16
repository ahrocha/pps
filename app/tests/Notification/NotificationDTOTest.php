<?php

use PHPUnit\Framework\TestCase;
use App\Notification\NotificationDTO;

class NotificationDTOTest extends TestCase
{
    public function testShouldCreateDtoFromValidArray(): void
    {
        $data = [
            'user_id' => 123,
            'message' => 'Notificação enviada com sucesso.',
        ];

        $dto = NotificationDTO::fromArray($data);

        $this->assertSame(123, $dto->userId);
        $this->assertSame('Notificação enviada com sucesso.', $dto->message);
    }

    public function testShouldThrowExceptionWhenUserIdIsMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Campos obrigatórios ausentes: user_id e/ou message.');

        NotificationDTO::fromArray([
            'message' => 'Texto qualquer'
        ]);
    }

    public function testShouldThrowExceptionWhenMessageIsMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Campos obrigatórios ausentes: user_id e/ou message.');

        NotificationDTO::fromArray([
            'user_id' => 1
        ]);
    }

    public function testShouldThrowExceptionWhenMessageExceedsLimit(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('A mensagem da notificação não pode exceder 180 caracteres.');

        NotificationDTO::fromArray([
            'user_id' => 1,
            'message' => str_repeat('a', 181)
        ]);
    }
}
