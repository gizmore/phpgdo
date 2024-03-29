<?php
namespace GDO\Core;

use GDO\UI\GDT_Headline;
use GDO\UI\GDT_Title;

trait WithTitleGDT
{

    public GDT $titleGDT;
    public GDT $subtitleGDT;

    public GDT $footerGDT;

    public function title(?GDT $gdt): static
    {
        unset($this->titleGDT);
        if ($gdt)
        {
            $this->titleGDT = $gdt;
        }
        return $this;
    }

    public function hasTitle(): bool
    {
        return isset($this->titleGDT);
    }

    public function titleRaw(string $title, bool $escaped=true): static
    {
        $this->titleGDT = GDT_Title::make()->titleRaw($title, $escaped);
        return $this;
    }

    public function titleNone(): static
    {
        return $this->title(null);
    }

    public function renderTitle(): string
    {
        return $this->titleGDT->render();
    }

    public function subtitle(?GDT $gdt): static
    {
        unset($this->subtitleGDT);
        if ($gdt)
        {
            $this->subtitleGDT = $gdt;
        }
        return $this;
    }

    public function subtitleRaw(string $title): static
    {
        $this->subtitleGDT = GDT_Headline::make()->textRaw($title, false)->level(5);
        return $this;
    }

    public function hasSubtitle(): bool
    {
        return isset($this->subtitleGDT);
    }

    public function renderSubTitle(): string
    {
        return $this->subtitleGDT->render();
    }


    public function subtitleNone(): static
    {
        return $this->subtitle(null);
    }

    public function footer(?GDT $footer): static
    {
        unset($this->footerGDT);
        if ($footer)
        {
            $this->footerGDT = $footer;
        }
        return $this;
    }

    public function hasFooter(): bool
    {
        return isset($this->footerGDT);
    }

    public function renderFooter(): string
    {
        return $this->footerGDT->render();
    }

}
