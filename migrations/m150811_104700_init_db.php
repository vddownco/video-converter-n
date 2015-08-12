<?php

use yii\db\Migration;

class m150811_104700_init_db extends Migration
{
    public function safeUp()
    {
        $this->createTable( 'user', [
            'id' => 'int(10) NOT NULL AUTO_INCREMENT',
            'username' => 'varchar(100) NOT NULL',
            'password' => 'varchar(60) NOT NULL',
            'PRIMARY KEY (`id`)',
            ],
            'ENGINE=InnoDB DEFAULT CHARSET=utf8'
        );
        $this->insert( 'user', [ 'username' => 'user1', 'password' => '$2y$13$D.0xAekUKkk8vgkZ/jCzcODv.VNIZx.7/cj0vg9M4aFr5ET3kEhdG' ]);
        $this->insert( 'user', [ 'username' => 'user2', 'password' => '$2y$13$Ez4FS9NNiap0VlCSRtoq3uCziHwBFUkCMKYp/AHHGbnJjxKyDJ74u' ]);
        $this->createTable( 'video', [
            'id' => 'int(10) NOT NULL AUTO_INCREMENT',
            'user_id' => 'int(10) NOT NULL',
            'original_id' => 'int(10) DEFAULT NULL',
            'name' => 'varchar(255) NOT NULL',
            'save_name' => 'varchar(255) NOT NULL',
            'width' => 'int(4) DEFAULT NULL',
            'height' => 'int(4) DEFAULT NULL',
            'video_bitrate' => 'int(5) DEFAULT NULL',
            'audio_bitrate' => 'int(5) DEFAULT NULL',
            'status' => 'int(3) DEFAULT NULL',
            'PRIMARY KEY (`id`)',
            ],
            'ENGINE=InnoDB DEFAULT CHARSET=utf8'
        );
        $this->createIndex( 'fk_video_user1_idx', 'video', 'user_id' );
        $this->createIndex( 'fk_video_video_idx', 'video', 'original_id' );
        $this->addForeignKey( 'fk_video_user', 'video', 'user_id', 'user', 'id', 'CASCADE', 'CASCADE' );
        $this->addForeignKey( 'fk_video_video', 'video', 'original_id', 'video', 'id', 'SET NULL', 'CASCADE' );
    }

    public function safeDown()
    {
        $this->dropTable( 'user' );
        $this->dropForeignKey( 'fk_video_video', 'video' );
        $this->dropForeignKey( 'fk_video_user', 'video' );
        $this->dropIndex( 'fk_video_video_idx', 'video' );
        $this->dropIndex( 'fk_video_user1_idx', 'video' );
        $this->dropTable( 'video' );
    }
}
