<?php

declare(strict_types=1);

namespace Fohn\Ui\AppTest\Model;

use Atk4\Data\Model;

class File extends Model
{
    public $table = 'file';

    protected function init(): void
    {
        parent::init();
        $this->addField('name');
        $this->addField('size', ['caption' => 'Size']);

        $this->addField('ext', ['caption' => 'MIME Type']);
        $this->addField('is_folder', ['type' => 'boolean']);

        // @phpstan-ignore-next-line
        $this->hasOne('parent_id', [
            'model' => [Folder::class],
        ])->addTitle();

        $this->hasMany('subFolder', [
            'model' => [self::class],
            'theirField' => 'parent_id',
        ])    // @phpstan-ignore-next-line
            ->addField('subCount', ['aggregate' => 'count', 'field' => $this->getPersistence()->expr($this, '*')]);
    }

    public function getFilesHierarchy(bool $isFolderSelectable = true): array
    {
        return $this->getHierarchicalTreeNodes($this->setOrder('is_folder', 'desc')->export(), null, $isFolderSelectable);
    }

    /**
     * Return Files Hierarchy as a TreeNode property.
     */
    private function getHierarchicalTreeNodes(array $files, ?int $useId = null, bool $isFolderSelectable): array
    {
        $parent = [];
        // Get top level files or folders.
        foreach ($files as $k => $file) {
            $nodeData = [
                'key' => $file['id'],
                'label' => ucfirst($file['name']),
                'type' => $file['ext'] !== '' ? 'file' : 'dir',
                'icon' => $file['ext'] !== '' ? 'bi bi-file-code' : 'bi bi-folder',
                'selectable' => $isFolderSelectable ? true : $file['ext'] !== '',
                'data' => [
                    'name' => $file['name'],
                    'size' => $file['size'],
                    'type' => $file['ext'] !== '' ? $file['ext'] : 'Folder',
                ],
            ];

            // Collect parent files or folders
            if (!$useId && !$file['parent_id']) {
                $parent[] = $nodeData;
            } elseif ($useId && $useId === $file['parent_id']) {
                $parent[] = $nodeData;
            }
        }

        // check if each top level parent has children
        foreach ($parent as $k => $file) {
            if ($file['data']['type'] === 'Folder') {
                $parent[$k]['children'] = $this->getHierarchicalTreeNodes($files, $file['key'], $isFolderSelectable);
            }
        }

        return $parent;
    }
}
